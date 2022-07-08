<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'label' => 'Site',
                'class' => Site::class,
                'choice_label' => function(?Site $site) {
                return $site ? strtoupper($site->getNom()) : '';
                },

                'choice_attr' => function(?Site $site) {
                return $site ? ['class' => 'site_'.strtolower($site->getNom())] : [];
                }
            ])
            ->add('bzbz', SearchType::class)
            ->add('Entre', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('et', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('filtreOrga', CheckboxType::class, [])
            ->add('filtreInscrit', CheckboxType::class, [])
            ->add('filtrePasInscrit', CheckboxType::class, [])
            ->add('filtreSortiesPasse', CheckboxType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
