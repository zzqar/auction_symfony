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
        $userE = $userRepository->find($id);
        $SpentMoney = 0;


        $arr = array() ;
        $arrRow = array();
        $goods = $goodsRepository->findAll();
        foreach ($goods as $g){

            $tr = $transactionRepository->findByMaxBetForGoodAndUser($userE,$g);
            if( $tr != 0){
                $status =$g->getStatus();
                if($status =='0'){
                    $status='Активен';
                }elseif ($status =='1'){
                    $status='Завершен';
                }else{
                    $status='Отменен';
                }

                if( $userE == $g->getUser()){
                    $SpentMoney +=  $tr;
                }

               array_push($arrRow , $g, $tr, $status, $g->getUser()) ;
               $arr[] = $arrRow;
                $arrRow = array();
            }
        }



        $form = $this->createForm(EditUserType::class );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userE->setName($form->get('name')->getData());
            $userRepository->add($userE,true);

            return $this->redirectToRoute('app_users_list');
        }
        return $this->render('user/index.html.twig', [
            'userE' => $userE,
            'user' => $user,
            'userForm' => $form->createView(),
            'arr'=> $arr,
            'SpentMoney'=>$SpentMoney,
        ]);
    }
}
