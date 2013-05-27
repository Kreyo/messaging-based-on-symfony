<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trivia\MessengerBundle\Entity\Messages;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormError;

class MessagesController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        }
        $messages = $this->get('doctrine')->getManager()
            ->createQueryBuilder()
            ->select('m')
            ->from('TriviaMessengerBundle:Messages', 'm')
            ->where('m.fromUser = :user OR m.toUser = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
        $index_paginator = $this->get('knp_paginator');
        $pagination = $index_paginator->paginate(
            $messages,
            $this->get('request')->query->get('page', 1),
            10/*limit per page*/
        );

        return $this->render('TriviaMessengerBundle:Messenger:index.html.twig',
            array('pagination' => $pagination,
                  'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                  'error' => $error,));
    }

    public function createAction(Request $request)
    {
      if($this->getUser()->getEmailToken() == null) {
        $message = new Messages();
        $form = $this->createFormBuilder()

            ->add('Recipient', 'text')
            ->add('text', 'textarea')
            ->getForm();
        if ($request->isMethod('POST')) {

            $form->bind($request);
            $formData = $form->getData();

            $message->setText($formData['text']);
            $message->setFromUser($this->getUser());
            $message->setUnread();
            $message->setToUser($this->getDoctrine()->getManager()->getRepository('TriviaMessengerBundle:Users')->findOneByUsername($formData['Recipient']));

            if ($form->isValid() && $this->getDoctrine()->getRepository('TriviaMessengerBundle:Users')->findOneByUsername($formData['Recipient'])!=null) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                return $this->redirect($this->generateUrl('trivia_messenger_homepage'));

            } else $form->get('Recipient')->addError(new FormError('You must enter an existing username!'));
        }

        return $this->render('TriviaMessengerBundle:Messenger:create.html.twig', array('form' => $form->createView(),));
      }
      else return $this->render('TriviaMessengerBundle:Messenger:cheater.html.twig');
    }

    public function viewAction($id)
    {
        $message = $this->getDoctrine()->getManager()->getRepository('TriviaMessengerBundle:Messages')->findOneById($this->getRequest()->get('id'));

        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        if ($this->getUser()->getUsername() == $message->getToUser()->getUsername()) {
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
            ->where('m.toUser = :user AND m.is_read = :false')
            ->setParameters(array(
                            'user' => $this->getUser(),
                            'false'=> false,
            ))
            ->orderBy('m.createdAt', 'DESC')
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
            ->orderBy('m.createdAt', 'DESC')
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
