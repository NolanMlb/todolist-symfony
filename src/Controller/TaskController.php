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
        // Check if we should edit or create a task
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
                //We copy the file in uploads folder
                $image -> move(
                    $this->getParameter('tasks_directory'),
                    $fichier
                );
                $task -> setImage($fichier);
            }
            // Set the user that create the task and add in the db
            $task -> setUser($this -> getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            // Redirection to home page
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
        // Get the task from db with ID in parameters
        $task = $entityManager->getRepository(Task::class)->find($id);

        // Check if task exists
        if (!$task) {
            throw $this->createNotFoundException('La tâche n\'existe pas');
        }

        // Delete task from db and file from uploads folder
        try {
            $entityManager->remove($task);
            $entityManager->flush();
            $file = $task -> getImage();
            if ($file) {
                unlink($this->getParameter('tasks_directory').'/'.$file);
            }
            $this -> addFlash('success', 'La tâche a été supprimée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors la suppression de la tâche');
        }


        return $this->redirectToRoute('app_index');
    }
}
?>