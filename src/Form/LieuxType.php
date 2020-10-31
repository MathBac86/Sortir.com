<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomLieu', TextType::class, [
                'label' => 'Nom du lieu : ',
                'attr' => [
                    'placeholder' => 'Nom du lieu'
                ]
            ])
            ->add('rueLieu', TextType::class, [
                'label' => 'Rue : ',
                'attr' => [
                    'placeholder' => 'Rue'
                ]
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Latitude'
                ]
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Latitude : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Longitude'
                ]
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville du lieu',
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'placeholder' => 'Ville'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieux::class,
        ]);
    }
}
