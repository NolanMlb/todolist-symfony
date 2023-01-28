<?php 
namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController {
    
    #[Route('/', name: 'app_index')]
    public function index(TaskRepository $taskRepository) {
        // get User logged on
        $user = $this -> getUser();
        $tasks = null;
        // if user, we get his tasks
        if ($user) {
            $tasks = $taskRepository->findBy(['user' => $user]);
        }
        return $this->render('index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
?>