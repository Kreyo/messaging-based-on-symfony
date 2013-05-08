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
            ->where('m.fromUser = :user OR m.toUser = :user')
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
        $form = $this->createFormBuilder()

            ->add('toUser', 'text')
            ->add('text', 'textarea')
            ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);
            $formData = $form->getData();



            $message->setText($formData['text']);
            $message->setFromUser($this->getUser());
            $message->setUnread();
            $message->setToUser($this->getDoctrine()->getManager()->getRepository('TriviaMessengerBundle:Users')->findOneByUsername($formData['toUser']));


            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
            }
        }
        return $this->render('TriviaMessengerBundle:Messenger:create.html.twig', array('form' => $form->createView(),));

    }

    public function viewAction($id){
        $message = $this->getDoctrine()->getManager()->getRepository('TriviaMessengerBundle:Messages')->findOneById($this->getRequest()->get('id'));

        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        if($this->getUser()->getUsername() == $message->getToUser()->getUsername()){
            $message->setRead();
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->render('TriviaMessengerBundle:Messenger:view.html.twig', array('message' => $message));
    }
    public function newAction()
    {
        $messages = $this->get('doctrine')->getManager()
            ->createQueryBuilder()
            ->select('m')
            ->from('TriviaMessengerBundle:Messages', 'm')
            ->where('m.is_read = :false AND m.fromUser = :user OR m.toUser = :user AND m.is_read = :false')
            ->setParameters(array(
                            'user' => $this->getUser(),
                            'false'=> false,
            ))

            ->getQuery()
            ->getResult();
        $index_paginator = $this->get('knp_paginator');
        $pagination = $index_paginator->paginate(
            $messages,
            $this->get('request')->query->get('page', 1),
            10/*limit per page*/
        );
        return $this->render('TriviaMessengerBundle:Messenger:new.html.twig', array('pagination' => $pagination ));
    }
    public function sentAction()
    {
        $messages = $this->get('doctrine')->getManager()
            ->createQueryBuilder()
            ->select('m')
            ->from('TriviaMessengerBundle:Messages', 'm')
            ->where('m.fromUser = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();
        $index_paginator = $this->get('knp_paginator');
        $pagination = $index_paginator->paginate(
            $messages,
            $this->get('request')->query->get('page', 1),
            10/*limit per page*/
        );

        return $this->render('TriviaMessengerBundle:Messenger:sent.html.twig', array('pagination' => $pagination ));
    }
}
