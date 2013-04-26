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
            ->createQueryBuilder()
            ->select('m')
            ->from('TriviaMessengerBundle:Messages', 'm')
            ->where('m.name = :user OR m.recipient = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();
        $index_paginator = $this->get('knp_paginator');
        $pagination = $index_paginator->paginate(
            $messages,
            $this->get('request')->query->get('page', 1),
            10/*limit per page*/
        );

        return $this->render('TriviaMessengerBundle:Messenger:index.html.twig', array('pagination' => $pagination ));
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
;
            $message->setUser();
            $message->setUnread();
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

    public function viewAction($id){
        $message = $this->getRepository('TriviaMessengerBundle:Messages')->findOneById($this->getRequest()->get('id'));

        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        if($this->getUser()->getUsername() == $message->getRecipient()){
            $message->setRead();
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->render('TriviaMessengerBundle :Messenger:view.html.twig', array('message' => $message));
    }

}
