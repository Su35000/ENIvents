<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('rue', TextType::class)
            ->add('coordonnees',TextType::class, [
                'mapped' =>false,
                'required'=>false
            ])
//            ->add('ville', EntityType::class,[
//                'label' => 'Ville',
//                'class'=> Ville::class,
//                'choice_label' => function(?Ville $ville) {
//                    return $ville ? strtoupper($ville->getNom()) : '';}
//            ])
//            ->add('cpo', TextType::class,[
//                'label' => 'Code Postal',
//                'mapped' =>false
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
