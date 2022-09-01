<?php

namespace App\Controller;

use App\Repository\GoodsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoodViewController extends AbstractController
{
    #[Route('/good/view/{id}',methods: ['GET'] )]
    public function index(int $id, GoodsRepository $hui): Response
    {
        $user = $this->getUser();
        $good = $hui->find($id);

        return $this->render('good_view/index.html.twig', [
            'user' => $user,
            'good' => $good ,
        ]);
    }


}
