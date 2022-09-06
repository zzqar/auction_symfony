<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\AddNewBetType;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', methods: ['GET','POST'])]
    public function index(Request $request,int $id, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $userE = $userRepository->find($id);



        $form = $this->createForm(EditUserType::class );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userE->setUsername($form->get('username')->getData());
            $userRepository->add($userE,true);

            return $this->redirectToRoute('app_users_list');
        }
        return $this->render('user/index.html.twig', [
            'userE' => $userE,
            'user' => $user,
            'userForm' => $form->createView(),
        ]);
    }
}
