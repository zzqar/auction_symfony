<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Form\AddGoodType;
use App\Repository\GoodsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddGoodController extends AbstractController
{
    #[Route('/addGood', name: 'app_add_good')]
    public function index(Request $request,GoodsRepository $goodsRepository): Response
    {
        $user = $this->getUser();
        $good = new Goods();

        $form = $this->createForm(AddGoodType::class, $good);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $good->setImages('/images/no_icon.png');

            $good->setDateCreate();
            $good->setCostmax($good->getCost()*10);

            $goodsRepository->add($good,true);
        }


        return $this->render('add_good/index.html.twig', [
            'user' => $user,
            'goodForm' => $form->createView(),
        ]);
    }
}
