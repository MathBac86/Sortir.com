<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus : ',
                'class' => Campus::class,
                'choice_label' => 'nomCampus',
            ])
            ->add('nom', TextType::class, [
                'label' => 'La sortie contient le mot : ',
                'empty_data' => null,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('dateDebut_du', TextType::class, [
                'label' => 'Date de sortie du : ',
               'attr' => [
                   'data-toggle'=>'datetimepicker',
                   'data-target'=>'#filter_close',
                   'placeholder' => 'Date du'
               ],
               'required'      => false,
               'empty_data' => null,
               'mapped' => false
            ])
            ->add('dateDebut_au', TextType::class, [
                'label' => 'au : ',
                'attr' => [
                    'data-toggle'=>'datetimepicker',
                    'data-target'=>'#filter_close',
                    'placeholder' => 'Date au'
                ],
                'required'      => false,
                'empty_data' => null,
                'mapped' => false
            ])
            ->add('organisateur',CheckboxType::class,  [
                'label'    => 'Sorties dont je suis organisateur',
                'empty_data' => null,
                'required' => false,
                'mapped' => false
            ])
            ->add('BeInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelles je suis inscrit',
                'empty_data' => null,
                'required'      => false,
                'mapped' => false
            ])
            ->add('NotInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit',
                'empty_data' => null,
                'required'      => false,
                'mapped' => false
            ])
            ->add('Finish',CheckboxType::class,  [
                'label'    => 'Sorties passées',
                'empty_data' => null,
                'required'      => false,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-success btn-large btn-default btn-block mb-2']
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
