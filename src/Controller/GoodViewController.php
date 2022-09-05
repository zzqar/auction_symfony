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
        $lastTransaction = $addTr->findOneBy([
            'good_id' => $id,
            'status' => '1'
        ]);
        $lastUser = New User();
        $error = Null;
        $msg = $good->getUser();

        $transaction = new Transaction();
        $form = $this->createForm(AddNewBetType::class, $transaction);
        $form->handleRequest($request);

        //Находим Максимальную ставку По товару : общую/пользователя
        $MaxBet = $addTr->findByMaxBetForGood($id);

        if ($form->isSubmitted() && $form->isValid())

        {
            //Сумма из формы
            $pay = $form->get('pay');

            //Находим баланс Пользователя
            $BillUser = $user->getBalance();
            $VirBillUser = $user->getVirBalance();

            // Проверяем на наличие прошлой ставки
            if (!is_null($lastTransaction)){
                $lastUser = $lastTransaction->getUserId();
                $lastPay = $lastTransaction->getPay();
            }else{
                $lastPay = 0;
            }

            if( $lastUser == $user  ){
                $error = 'Нельзя ставить более 1 раза подрят';

            }elseif( $pay->getNormData() <= $good->getCost() ){
                $error = 'Ставка должна превышать начальную стоимость';

            }elseif( $pay->getNormData() <= $lastPay ){
                $error = 'Ставка должна превышать последнюю ставку';

            }elseif( $pay->getNormData() > $good->getCostmax() ){
                $error = 'Ставка не должна превышать стоимость быстрого выкупа';

            }elseif( $VirBillUser < $pay->getNormData() ){
                $error = 'Не достаточно средств для ставки';

            }else{

                if( !is_null($lastTransaction) ){
                    //обновление пред. ставки
                    $lastTransaction->setStatus(false);
                    $addTr->add($lastTransaction);

                    //возврашяем вир. баланс пользователя
                    $lastUser ->setVirBalance($lastUser ->getVirBalance() + $lastTransaction->getPay());
                }

                //Добавление сставки
                $transaction->setGoodId($good);
                $transaction->setUserId($user);
                $transaction->setDate();
                $transaction->setPay($pay->getNormData());
                $transaction->setStatus(true);
                $addTr->add($transaction,true);

                //Обновляем вир. баланс пользователя vir VirBalance - ставка
                $user->setVirBalance($VirBillUser - $pay->getNormData());
                $userRepository->add($user,true);

                //Если ставка равна Предельной стоимости товара->Присваеваем тавар пользователю
                if($pay->getNormData() == $good->getCostmax()){
                $good->setUser($user);
                $hui->add($good,true);
                }
            }
        }

        return $this->render('good_view/index.html.twig', [
            'good' => $good ,
            'user' => $user,
            'goodForm' => $form->createView(),
            'error' => $error,
            'lastBet'=>$MaxBet,
            'msg'=> $msg,
        ]);
    }


}
