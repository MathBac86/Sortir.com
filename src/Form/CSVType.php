<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Cvs', FileType::class, [
                'label'=> 'Télécharger un fichier .csv : ',
                'mapped'=>false,
                'required'=>false,
                'attr'=> [
                    'class'=> "inputUpload"
                ],
                'constraints'=>[
                    new File([
                        'maxSize'=>'1024k'
                    ])
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
