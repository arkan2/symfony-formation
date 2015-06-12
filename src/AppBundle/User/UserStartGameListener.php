<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 11/06/2015
 * Time: 11:46
 */

namespace AppBundle\User;


use AppBundle\Game\GameEvent;
use AppBundle\Game\GameEvents;
use Composer\EventDispatcher\EventSubscriberInterface;

class UserStartGameListener implements EventSubscriberInterface {

    private $userManager;
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, UserManager $userManager) {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            GameEvents::STARTED => 'onGameStarted'
        ];
    }

    public function onGameStarted(GameEvent $event)
    {
        $user = $this->getTokenStorage()->getUser();
        $user->debitBalance(100);
        $this->userManager->save($user);
    }

    /**
     * @return mixed
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param mixed $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return mixed
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @param mixed $tokenStorage
     */
    public function setTokenStorage($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


}