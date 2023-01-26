<?php
namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_add_task')]
    #[Route('/task/{id}/edit', name: 'app_edit_task')]
    public function addTask(Task $task = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$task) {
            $task = new Task();
        }
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                //on copie le fichier dans le dossier uploads
                $image -> move(
                    $this->getParameter('tasks_directory'),
                    $fichier
                );
                $task -> setImage($fichier);
            }
            $task -> setUser($this -> getUser());
            $entityManager->persist($task);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute("app_index");
        }
        return $this->render('task/createTask.html.twig', [
            'CreateTaskForm' => $form->createView(),
            'editMode'=> $task-> getId() !== null
        ]);
    }

    #[Route('/task/{id}/delete', name: 'app_delete_task')]
    public function deleteTask($id, EntityManagerInterface $entityManager)
    {
        // Récupérer la tâche à partir de la base de données en utilisant l'ID fourni en paramètre
        $task = $entityManager->getRepository(Task::class)->find($id);

        // Vérifier si la tâche existe
        if (!$task) {
            throw $this->createNotFoundException('La tâche n\'existe pas');
        }

        // Supprimer la tâche de la base de données
        $entityManager->remove($task);
        $entityManager->flush();

        // Rediriger l'utilisateur vers la page de liste des tâches
        return $this->redirectToRoute('app_index');
    }
}
?>