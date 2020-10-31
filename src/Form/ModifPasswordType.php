<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
