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
    public function addTask(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
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
        ]);
    }
}
?>