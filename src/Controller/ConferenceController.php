<?php

namespace App\Controller;

use App\Repository\GoodsRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    #[Route('/goods_list', name: 'app_conference')]
    public function index(GoodsRepository $goodsRepository): Response
    {
        $goods = $goodsRepository->findAll();

        $user = $this->getUser();

        return $this->render('conference/index.html.twig', [
            'goods' =>  $goods,
            'user' => $user,
        ]);
    }


}
