<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 11/06/2015
 * Time: 17:08
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType {

    public function getParent() {
        return 'text';
    }

    public function getName()
    {
        return 'app_phone';
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {

    }
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['view_format' => 'dd-dd-dd-dd-dd']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = preg_replace('/\D/','',trim($event->getData()));
            $event->setData($data);
        });
    }


}