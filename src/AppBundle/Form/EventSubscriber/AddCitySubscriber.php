<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 12/06/2015
 * Time: 11:14
 */
namespace AppBundle\Form\EventSubscriber;

class AddCitySubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface {


    public static function getSubscribedEvents()
    {
        return [
            \Symfony\Component\Form\FormEvents::PRE_SUBMIT => 'onPreSetData'
        ];
    }

    public function onPreSetData(\Symfony\Component\Form\FormEvent $event) {
        $form = $event->getForm();
        $cities = [
            'FR' => [
                'Paris' => 'Paris',
                'Lyon' => 'Lyon',
                'Nancy' => 'Nancy'
            ]
            // ...
        ];

        $data = $event->getData();
        $country = $data['country'];

        $form = $event->getForm();
        $form->add('city', 'choice', [
            'choices' => $cities[$country]
        ]);
    }
}