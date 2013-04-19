<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trivia\MessengerBundle\Entity\Messages;

class MessagesController extends Controller
{
    public function indexAction()
    {
        return $this->render('TriviaMessengerBundle:Messenger:base.html.twig');
    }

    public function createAction(Request $request)
    {
        $message = new Messages();
        $message->setName('Your name');
        $message->setRecipient('Your recipient here');
        $message->setText('Your message text here');
        $form = $this->createFormBuilder($message)
            ->add('name', 'text')
            ->add('recipient', 'text')
            ->add('text', 'text')
            ->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                return $this->redirect($this->generateUrl('messages'));
            }
        }
        return $this->render('TriviaMessengerBundle:Messenger:create.html.twig', array('form' => $form->createView(),));

    }

}
