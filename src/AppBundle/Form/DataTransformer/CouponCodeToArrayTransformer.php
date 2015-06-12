<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 11/06/2015
 * Time: 18:25
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Model\CouponCode;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Validator\Constraints\DateTime;

class CouponCodeToArrayTransformer implements DataTransformerInterface {

    // Pour simuler les données en base
    static private $couponList = [
        'test@mail.zz' => [
            'code' => 'ABCDEF',
            'earned_credits' => 500,
            'expires_at' => '2015-06-30'
        ]
    ];

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param CouponCode $coupon The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($coupon)
    {
        if($coupon === null) {
            return '';
        }

        if($coupon instanceof CouponCode) {
            return ['code' => $coupon->getCode(),
                    'email' => $coupon->getEmail()];
        }
        else {
            throw new TransformationFailedException();
        }

    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param array $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($data)
    {
        if($data === '') {
            return null;
        }

        if(!is_array($data)) {
            throw new TransformationFailedException();
        }

        if(empty($data['code']) || empty($data['email'])) {
            throw new TransformationFailedException();
        }

        if(empty(self::$couponList[$data['email']])) {
            throw new TransformationFailedException();
        }

        return new CouponCode($data['code'], $data['email'], self::$couponList[$data['email']]['expires_at']);
    }


}