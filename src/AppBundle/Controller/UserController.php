<?php

namespace AppBundle\Controller;

use AppBundle\Form\RegistrationType;
use AppBundle\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/login", name="app_login")
     * @Method("GET")
     */
    public function loginAction()
    {
        $utils = $this->get('security.authentication_utils');

        return $this->render('user/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login", name="app_login_check")
     * @Method("POST")
     */
    public function loginCheckAction()
    {
        // this body is never executed
    }

    /**
     * @Route("/logout", name="app_logout")
     * @Method("GET")
     */
    public function logoutAction()
    {
        // this body is never executed
    }

    /**
     * @Route("/register", name="app_register")
     * @Method("GET|POST")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new RegistrationType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('app.user_manager')->save($user);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Cache(smaxage=60)
     */
    public function sidebarAction()
    {
        $users = $this->get('app.user_manager')->findMostRecentUsers(5);

        return $this->render('user/sidebar.html.twig', [
            'users' => $users,
        ]);
    }
}
