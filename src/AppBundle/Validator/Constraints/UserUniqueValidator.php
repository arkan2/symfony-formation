<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\User\User;
use AppBundle\User\UserManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserUniqueValidator extends ConstraintValidator
{
    private $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    public function validate($user, Constraint $constraint)
    {
        if (!$constraint instanceof UserUnique) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UserUnique');
        }

        if (!$user instanceof User) {
            throw new UnexpectedTypeException($user, 'AppBundle\User\User');
        }

        $username = $user->getUsername();
        if (false !== $this->manager->findByUsername($username)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $username)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $username)
                    ->addViolation();
            }
        }
    }
}
