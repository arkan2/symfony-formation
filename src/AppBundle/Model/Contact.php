<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=60)
     */
    public $fullName;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $emailAddress;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=80)
     */
    public $subject;

    /**
     * @Assert\NotBlank
     */
    public $message;

    public function toMail()
    {
        $message = \Swift_Message::newInstance()
            ->setFrom($this->emailAddress)
            ->setTo('admin@monsite.com')
            ->setSubject($this->subject)
            ->setBody($this->message)
        ;

        return $message;
    }
}
