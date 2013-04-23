<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trivia\MessengerBundle\Entity\Messages;

class MessagesController extends Controller
{
    public function indexAction()
    {
        $messages = $this->get('doctrine')->getManager()
            ->createQuery('SELECT all FROM TriviaMessengerBundle:Messages ')
            ->execute();
        $current_user= $this->get('security.context')->getToken()->getUser();
        $current_user->getUsername();
        return $this->render('TriviaMessengerBundle:Messenger:base.html.twig', array('messages' => $messages, 'current' => $current_user));
    }

    public function createAction(Request $request)
    {
        $message = new Messages();

        $message->setRecipient('Your recipient here');
        $message->setText('Your message text here');
        $form = $this->createFormBuilder($message)
            ->add('name', 'text')
            ->add('recipient', 'text')
            ->add('text', 'textarea')
            ->getForm();
        if ($request->isMethod('POST')) {
            $current_user= $this->get('security.context')->getToken()->getUser();
            $current_user->getUsername();
            $message->setName($current_user);
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
