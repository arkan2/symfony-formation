<?php

namespace AppBundle\Form;

use AppBundle\Form\AddCitySubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('fullName', 'text')
            ->add('email', 'email')
            ->add('password', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'register.password.mismatch',
                'first_options' => [
                    'label' => 'register.form.password',
                ],
                'second_options' => [
                    'label' => 'register.form.confirmation',
                ]
            ])
            ->add('phoneNumber','app_phone', [
                'required' => false,
            ])
            ->add('birthdate', 'birthday', [
                'required' => false,
            ])
            ->add('country', 'country')
            ->add('couponCode','app_coupon_code', [
                'required' => false
            ])
            ->add('terms', 'checkbox')
        ;

        $builder->addEventSubscriber(new \AppBundle\Form\EventSubscriber\AddCitySubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Model\AccountRegistration',
            'translation_domain' => 'user',
            //'validation_groups' => [ 'Default', 'Signup' ],
        ]);
    }

    public function getName()
    {
        return 'app_registration';
    }
}
