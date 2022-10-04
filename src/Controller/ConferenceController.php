<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Form\AddGoodType;
use App\Repository\GoodsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    #[Route('/goods_list', name: 'app_conference')]
    public function index(GoodsRepository $goodsRepository,Request $request): Response
    {
        $goods = $goodsRepository->findAll();
        $user = $this->getUser();
        $good = new Goods();

        $form = $this->createForm(AddGoodType::class, $good);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $good->setImages('/images/no_icon.png')->setDateCreate()->setCostmax($good->getCost()*10);
            $goodsRepository->add($good,true);

            return $this->redirectToRoute('app_conference'  );
        }


        return $this->render('conference/index.html.twig', [
            'goods' =>  $goods,
            'user' => $user,
            'goodForm' => $form->createView(),
        ]);
    }


}
