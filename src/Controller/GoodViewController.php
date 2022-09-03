<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Entity\Transaction;
use App\Form\AddGoodType;
use App\Form\AddNewBetType;
use App\Repository\GoodsRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoodViewController extends AbstractController
{
    #[Route('/good/view/{id}',methods: ['GET','POST'] )]
    public function index(Request $request, int $id, GoodsRepository $hui, TransactionRepository $addTr,UserRepository $userRepository): Response
    {

        $user = $this->getUser();
        $good = $hui->find($id);


        $transaction = new Transaction();

        $form = $this->createForm(AddNewBetType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $pay = $form->get('pay');

            //Находим баланс Пользователя
            $BillUser = $user->getBalance();
            $VirBillUser = $user->getVirBalance();
            //

            //Находим Максимальную ставку По товару : общую/пользователя

            $MaxBet = $addTr->findByMaxBetForGood($good->getId());

            $c= 1+1;


            //Находим Разницу в ставках пользователя : $pay - (Максиальная ставка пользователя)

            //

            //Проверки: Достаточность средств;  Наличие такой же ставки и выше;

            //

            //Добавление сставки

            //

            //Если ставка равна Предельной стоимости товара->Восстоновить Вир Балансы Пользователей
            //-> Вычитаю стоимость ставки из Баланса и Вир. Баланса Победителя

            //

            $transaction->setGoodId($good);
            $transaction->setUserId($user);
            $transaction->setDate();
            $transaction->setTotal('100');

            //$addTr->add($transaction,true);
        }
        $b =$form->createView();
        return $this->render('good_view/index.html.twig', [
            'good' => $good ,
            'user' => $user,
            'goodForm' => $form->createView(),
        ]);
    }


}
