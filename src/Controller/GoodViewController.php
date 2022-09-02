<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\AddGoodType;
use App\Form\AddNewBetType;
use App\Repository\GoodsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoodViewController extends AbstractController
{
    #[Route('/good/view/{id}',methods: ['GET'] )]
    public function index(Request $request, int $id, GoodsRepository $hui): Response
    {
        $user = $this->getUser();
        $good = $hui->find($id);

        $transaction = new Transaction();

        $form = $this->createForm(AddNewBetType::class, $transaction);
        $form->handleRequest($request);

        return $this->render('good_view/index.html.twig', [
            'good' => $good ,
            'user' => $user,
            'goodForm' => $form->createView(),
        ]);
    }


}
