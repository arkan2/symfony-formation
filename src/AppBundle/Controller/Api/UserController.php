<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\AccountRegistration;
use AppBundle\User\User;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    /**
     * @Get("/users/{username}", name="api_users_show")
     * @View(serializerGroups={ "Default" })
     */
    public function showAction($username)
    {
        $manager = $this->get('app.user_manager');
        if (!$user = $manager->findByUsername($username)) {
            throw $this->createNotFoundException();
        }

        return $user;
    }

    /**
     * @Post("/users", name="api_users_create")
     * @ParamConverter("user", converter="fos_rest.request_body", options={
     *   "deserializationContext"={
     *     "groups"={"Create", "Default"}
     *   }
     * })
     */
    public function createAction(User $user, ConstraintViolationListInterface $violations)
    {
        if (count($violations)) {
            return $this->view($violations, 400);
        }

        $this->get('app.user_manager')->save($user);

        return $this->redirectView(
            $this->generateUrl('api_users_show', [ 'username' => $user->getUsername() ]),
            201
        );
    }
}
