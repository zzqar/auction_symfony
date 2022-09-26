<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\AddNewBetType;
use App\Form\EditUserType;
use App\Repository\GoodsRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', methods: ['GET','POST'])]
    public function index(Request $request,int $id,GoodsRepository$goodsRepository, UserRepository $userRepository, TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser();
        $userTarget = $userRepository->find($id);
        $SpentMoney = 0;

        $arr = array();
        $arrRow = array();
        $goods = $goodsRepository->findAll();
        foreach ($goods as $good){

            $transactionForGood = $transactionRepository->findByMaxBetForGoodAndUser($userTarget,$good);

            if( $transactionForGood != 0){
                $status = $good->getStatus();
                if($status == '0'){
                    $status = 'Активен';
                }elseif ($status =='1'){
                    $status = 'Завершен';
                }else{
                    $status = 'Отменен';
                }
                if( $userTarget == $good->getUser()){
                    $SpentMoney +=  $transactionForGood;
                }
               array_push($arrRow , $good, $transactionForGood, $status, $good->getUser()) ;
               $arr[] = $arrRow;
                $arrRow = array();
            }
        }

        $form = $this->createForm(EditUserType::class );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTarget->setName($form->get('name')->getData());
            $userRepository->add($userTarget,true);

            return $this->redirectToRoute('app_users_list');
        }
        return $this->render('user/index.html.twig', [
            'userE' => $userTarget,
            'user' => $user,
            'userForm' => $form->createView(),
            'arr'=> $arr,
            'SpentMoney'=>$SpentMoney,
        ]);
    }
}
