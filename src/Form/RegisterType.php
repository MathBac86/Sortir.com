<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Votre Pseudo : ',
                'attr' => [
                    'placeholder' => 'Pseudo'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre Nom : ',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre Prénom : ',
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Votre Téléphone : ',
                'attr' => [
                    'placeholder' => 'Téléphone'
                ]
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Votre Email : ',
                'attr' => [
                    'placeholder' => 'Mail'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Votre Photo : ',
                'required' => false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, 'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']], 'required' => true,
                'first_options' => [
                    'label' => 'Votre Password : ',
                    'attr' => [
                        'placeholder' => 'Password'
                    ]],
                'second_options' => [
                    'label' => 'Répéter votre Password : ',
                    'attr' => [
                        'placeholder' => 'Confirmation password'
                    ]],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Votre Campus : ',
                'class' => Campus::class,
                'choice_label' => 'nomCampus',
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
