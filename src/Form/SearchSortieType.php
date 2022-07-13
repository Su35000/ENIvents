<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Site;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville', EntityType::class, [
                'required' => false,
                'mapped' =>false,
                'label' => 'Ville',
                'class' => Ville::class,
                'choice_label' => function(?Ville $ville) {
                return $ville ? strtoupper($ville->getNom()) : '';
                },

                'choice_attr' => function(?Ville $ville) {
                return $ville ? ['class' => 'ville_'.strtolower($ville->getNom())] : [];
                }
            ])
            ->add('le_nom_de_la_sortie_contient', TextType::class, [
                'mapped' =>false,
                'required' => false,
            ])
            ->add('entre', DateTimeType::class, [
                'mapped' =>false,
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('et', DateTimeType::class, [
                'mapped' =>false,
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('filtreOrga', CheckboxType::class, [
                'mapped' =>false,
                'required' => false,
                'label' => "J'organise",
            ])
            ->add('filtreInscrit', CheckboxType::class, [
                'mapped' =>false,
                'required' => false,
                'label' => "J'y suis inscrit",
            ])
//            ->add('filtrePasInscrit', CheckboxType::class, [
//                'mapped' =>false,
//                'required' => false,
//            ])
            ->add('filtreSortiesPasse', CheckboxType::class, [
                'mapped' =>false,
                'required' => false,
                'label' => 'Sorties Passées',
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
