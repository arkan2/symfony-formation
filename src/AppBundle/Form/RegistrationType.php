<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('fullName', 'text')
            ->add('emailAddress', 'email')
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'register.password.mismatch',
                'first_options' => [
                    'label' => 'register.form.password',
                ],
                'second_options' => [
                    'label' => 'register.form.confirmation',
                ]
            ])
            ->add('birthdate', 'birthday', [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\User\User',
            'translation_domain' => 'user',
            'validation_groups' => [ 'Default', 'Signup' ],
        ]);
    }

    public function getName()
    {
        return 'app_registration';
    }
}
