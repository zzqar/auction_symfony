<?php

namespace App\Controller;

use App\Entity\Goods;
use App\Form\AddGoodType;
use App\Repository\GoodsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConferenceController extends AbstractController
{
    #[Route('/', name: 'app_conference')]
    public function index(GoodsRepository $goodsRepository,Request $request, SluggerInterface $slugger): Response
    {
        $goods = $goodsRepository->findAll();
        $user = $this->getUser();
        $good = new Goods();

        $form = $this->createForm(AddGoodType::class, $good);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
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

                // обновляет свойство 'brochureFilename' для сохранения имени PDF-файла,
                // а не его содержания
                $good->setImages($newFilename);
            }else{
                $good->setImages('no_icon.png');
            }

            $good->setDateCreate()->setCostmax($good->getCost()*10);
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
