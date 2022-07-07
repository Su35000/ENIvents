<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('duree', NumberType::class,[
                'scale'=> 1,
                'label' => 'Durée de la sortie (heure)'
            ])
            ->add('dateCloture',DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscritMax',NumberType::class,[
                'label' => "Nombre max d'inscrits",
                'scale'=> 1
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
//            ->add('ville', EntityType::class,[
//                'label' => 'Ville',
//                'mapped' =>false,
//                'class'=> Ville::class,
//                'choice_label' => function(?Ville $ville) {
//                    return $ville ? strtoupper($ville->getNom()) : '';}
//            ])
            ->add('lieu', EntityType::class,[
                'label' => 'Lieu',
                'class'=> Lieu::class,
                'choice_label' => function(?Lieu $lieu) {
                    return $lieu ? strtoupper($lieu->getNom()) : '';}
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
