<?php

namespace AppBundle\User;

use AppBundle\Validator\Constraints\UserUnique;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @UserUnique
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("user")
 */
class User implements UserInterface
{
    /**
     * @Assert\NotBlank(groups={ "Signup", "Toto" })
     * @Assert\Length(min=5, max=40, groups="Signup")
     * @Assert\Regex(
     *   pattern="/^[a-z0-9]+$/i",
     *   message="user.username.invalid_format",
     *   groups="Signup"
     * )
     *
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Groups({ "Default" })
     */
    private $username;

    private $password;
    private $salt;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=50)
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Groups({ "Default" })
     */
    private $fullName;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Groups({ "Default" })
     */
    private $emailAddress;

    /**
     * @Assert\Range(max="-18 years")
     * @Serializer\Expose
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Groups({ "Default" })
     */
    private $birthdate;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({ "Default" })
     */
    private $permissions;

    /**
     * @Assert\NotBlank(groups="Signup")
     * @Assert\Length(min=8)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Groups({ "Create" })
     */
    public $plainPassword;

    public function __construct()
    {
        $this->permissions[] = 'ROLE_PLAYER';
    }

    /**
     * @Assert\Callback
     */
    public function checkPassword(ExecutionContextInterface $context)
    {
        if (false !== stripos($this->password, $this->username)) {
            $context
                ->buildViolation('user.password.username_detected')
                ->atPath('password')
                ->addViolation()
            ;
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
        if (!$birthdate instanceof \DateTime) {
            $birthdate = new \DateTime($birthdate);
        }

        $this->birthdate = $birthdate;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function getRoles()
    {
        $roles = [];
        if (!$this->permissions) {
            return $roles;
        }

        foreach ($this->permissions as $permission) {
            $roles[] = new Role($permission);
        }

        return $roles;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
