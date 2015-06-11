<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
use AppBundle\Model\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MiscController extends Controller
{
    /**
     * @Route("/contact", name="app_contact")
     * @Method("GET|POST")
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        $form->add('submit', 'submit');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('mailer')->send($contact->toMail());
            $this->addFlash('success', 'contact.success');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('misc/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
