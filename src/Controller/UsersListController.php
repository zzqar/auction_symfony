<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersListController extends AbstractController
{
    #[Route('/users/list', name: 'app_users_list')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $AllUsers = $userRepository->findAll();


        return $this->render('users_list/index.html.twig', [
            'user' => $user,
            'allUsers'=>$AllUsers,

        ]);
    }
}
