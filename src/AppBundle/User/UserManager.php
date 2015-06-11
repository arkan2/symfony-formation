<?php

namespace AppBundle\User;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

class UserManager implements UserProviderInterface
{
    private $location;
    private $saltGenerator;
    private $passwordEncoder;

    public function __construct(
        $location,
        SecureRandomInterface $saltGenerator = null,
        UserPasswordEncoderInterface $passwordEncoder = null
    )
    {
        $this->location = $location;
        $this->saltGenerator = $saltGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function findMostRecentUsers($max)
    {
        $iterator = Finder::create()
            ->files()
            ->sortByModifiedTime()
            ->in($this->location);

        $files = iterator_to_array($iterator);
        rsort($files);
        $files = array_slice($files, 0, $max);

        $usernames = [];
        foreach ($files as $file) {
            $usernames[] = $file->getBasename('.txt');
        }

        $users = [];
        foreach ($usernames as $username) {
            $users[] = $this->findByUsername($username);
        }

        return $users;
    }

    private function updateCredentials(User $user)
    {
        $user->setSalt(sha1($this->saltGenerator->nextBytes(40)));

        $password = $this
            ->passwordEncoder
            ->encodePassword($user, $user->plainPassword)
        ;

        $user->setPassword($password);
        $user->eraseCredentials();
    }

    public function save(User $user)
    {
        if ($user->plainPassword) {
            $this->updateCredentials($user);
        }

        $account = $this->findByUsername($user->getUsername());

        // trying to create a user account with a username
        // that already exists but has a different case.
        if ($account
            && $account->getUsername() !== $user->getUsername()
            && strtolower($account->getUsername()) === strtolower($user->getUsername())
        ) {
            throw new UserAlreadyExistsException($user->getUsername());
        }

        $path = $this->getStoragePath($user->getUsername());

        if (!file_exists($path)) {
            touch($path);
            chmod($path, 0777);
        }

        if (!is_writable($path)) {
            throw new \InvalidArgumentException(sprintf('%s must be writable.', $path));
        }

        file_put_contents($path, serialize($user));
    }

    public function findByUsername($username)
    {
        $path = $this->getStoragePath($username);

        if (!file_exists($path)) {
            return false;
        }

        if (!is_readable($path)) {
            throw new \InvalidArgumentException(sprintf('%s must be readable.', $path));
        }

        return unserialize(file_get_contents($path));
    }

    private function getStoragePath($username)
    {
        return sprintf('%s/%s.txt', $this->location, strtolower($username));
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findByUsername($username);
        if (false === $user) {
            throw new UsernameNotFoundException(sprintf(
                'User "%s" does not exist.',
                $username
            ));
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf(
                'Users of type %s are not supported by this provider.',
                $class
            ));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'AppBundle\User\User' === $class;
    }
}
