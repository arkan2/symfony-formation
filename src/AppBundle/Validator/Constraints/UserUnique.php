<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UserUnique extends Constraint
{
    public $message = 'user.username.already_exists';

    public function validatedBy()
    {
        return 'app.user_unique_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
