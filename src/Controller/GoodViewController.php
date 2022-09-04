<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Entity\User;
use App\Entity\Transaction;
use App\Form\AddGoodType;
use App\Form\AddNewBetType;
use App\Repository\GoodsRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\String_;
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
        $error = Null;

        $transaction = new Transaction();
        $form = $this->createForm(AddNewBetType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            //Сумма из формы
            $pay = $form->get('pay');

            //Находим Максимальную ставку По товару : общую/пользователя
            $MaxBet = $addTr->findByMaxBetForGood($id);
            $MaxBetForUser = $addTr->findByMaxBetForGoodAndUser($id, $user);
            //

            //Находим баланс Пользователя
            $BillUser = $user->getBalance();
            $VirBillUser = $user->getVirBalance();
            //

            //Находим Разницу в ставках пользователя : $pay - (Максиальная ставка пользователя)
            $difPay = $pay->getNormData() - $MaxBetForUser;
            //

            if( $pay->getNormData() <= $good->getCost() ){
                $error = 'Ставка должна превышать начальную стоимость';

            }elseif( $pay->getNormData() <= $MaxBet ){
                $error = 'Ставка должна превышать последнюю ставку';

            }elseif( $pay->getNormData() > $good->getCostmax() ){
                $error = 'Ставка не должна превышать стоимость быстрого выкупа';

            }elseif( $VirBillUser < $difPay ){
                $error = 'Не достаточно средств для ставки';

            }else{
                //Добавление сставки
                $transaction->setGoodId($good);
                $transaction->setUserId($user);
                $transaction->setDate();
                $transaction->setTotal($pay->getNormData());
                //$addTr->add($transaction,true);
            }


            //Если ставка равна Предельной стоимости товара->Восстоновить Вир Балансы Пользователей
            //-> Вычитаю стоимость ставки из Баланса и Вир. Баланса Победителя

            //

        }

        return $this->render('good_view/index.html.twig', [
            'good' => $good ,
            'user' => $user,
            'goodForm' => $form->createView(),
            'error' => $error,
        ]);
    }


}
