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
        $user = $this -> getUser();
        $tasks = [];
        if ($user) {
            $userId = $user -> getId();
            $tasks = $taskRepository->findBy(['user' => $userId]);
        }
        return $this->render('index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
?>