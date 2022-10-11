<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Form\AddGoodType;
use App\Repository\GoodsRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConferenceController extends AbstractController
{
    #[Route('/', name: 'app_conference' ,methods: ['GET','POST'])]
    public function index(GoodsRepository $goodsRepository,Request $request, SluggerInterface $slugger): Response
    {
        $goods = $goodsRepository->findAll();
        $user = $this->getUser();
        $good = new Goods();

        $form = $this->createForm(AddGoodType::class, $good);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $date = date('Y-m-d');
            $date = date("Y-m-d", strtotime($date.'+ 1 days'));

            $last_date= $form->get('last_date')->getNormData()->format('Y-m-d');

            if($form->get('cost')->getNormData() <= 100 or $last_date < $date  ){
                $error = 'Наушенны форматы в форме добавления товаров';

                return $this->redirectToRoute('app_conference',[
                    'error' => $error,
                ]);
            }

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('images')->getData();


            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // это необходимо для безопасного включения имени файла в качестве части URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Переместите файлв каталог, где хранятся брошюры
                try {
                    $imageFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... разберитесь с исключением, если что-то случится во время загрузки файла
                }


                $good->setImages($newFilename);
            }else{
                $good->setImages('no_icon.png');
            }

            $good->setDateCreate()->setCostmax($good->getCost()*10);
            $goodsRepository->add($good,true);

            return $this->redirectToRoute('app_conference'  );
        }

        $error =  [];
        if( !empty($_GET['error']) ){
            $error = $_GET['error'];
        }


        return $this->render('conference/index.html.twig', [
            'goods' =>  $goods,
            'user' => $user,
            'goodForm' => $form->createView(),
            'error' => $error,
        ]);
    }


}
