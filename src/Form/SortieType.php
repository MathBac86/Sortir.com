<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la Sortie : ',
                'attr' => [
                    'placeholder' => 'Nom de la sortie'
                ]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de la Sortie : ',
                'widget' => 'single_text',
                'html5' => 'true'
            ])
            ->add('datecloture', DateType::class, [
                'label' => 'Date limite d\'Inscription : ',
                'widget' => 'single_text',
                'html5' => 'true',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place : ',
                'attr' => [
                    'placeholder' => 'Nbre max'
                ]
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes) : ',
                'attr' => [
                    'placeholder' => 'Durée'
                ]
            ])
            ->add('descriptioninfo', TextareaType::class, [
                'label' => 'Description et infos : ',
                'attr' => [
                    'placeholder' => 'Description'
                ]
            ])
            ->add('Lieux', EntityType::class, [
                'label' => 'Lieux : ',
                'class' => Lieux::class,
                'choice_label' => 'nomLieu',
                'attr' => [
                    'placeholder' => 'Le lieu'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
