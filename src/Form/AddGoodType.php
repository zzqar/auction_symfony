<?php

namespace App\Form;

use App\Entity\Goods;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddGoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $date = new \DateTime();
        $date->modify('+1 day');

        $builder
            ->add('images', FileType::class, [

                // неотображенное означает, что это поле не ассоциировано ни с одним свойством сущности
                'mapped' => false,

                // сделайте его необязательным, чтобы вам не нужно было повторно загружать PDF-файл
                // каждый раз, когда будете редактировать детали Product
                'required' => false,


            ])
            ->add('name'  )
            ->add('cost', IntegerType::class, [
                    'attr' => [
                        'min' => 100,
                        'max' => 100000,
                    ]
                ])
            ->add('last_date', DateType::class,[
                'widget' => 'single_text',
                'data'   => $date,
                'attr'   => [
                    'min' => $date->format('Y-m-d')
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Goods::class,
        ]);
    }
}
