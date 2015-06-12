<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 11/06/2015
 * Time: 17:08
 */

namespace AppBundle\Form;


use AppBundle\Form\DataTransformer\CouponCodeToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CouponCodeType extends AbstractType {

    public function getParent() {
        return 'text';
    }

    public function getName()
    {
        return 'app_coupon_code';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(is_array($view->vars['value'])) {
            $view->vars['value'] = $view->vars['value']['code']; // Pas joli, possible de remplacer le tableau par un objet
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Model\CouponCode',
            'invalid_message' => 'Coupon Invalide'
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            $code = $event->getData();
            $email = $event->getForm()->getParent()->get('email')->getData();

            $event->setData([
                'code' => $code,
                'email' => $email
            ]);
        });

        $builder->addModelTransformer(new CouponCodeToArrayTransformer());
    }


}