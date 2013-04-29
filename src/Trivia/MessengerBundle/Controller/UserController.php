<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trivia\MessengerBundle\Entity\Users;
use Symfony\Component\Security\Core\SecurityContext;
class UserController extends Controller{
    public function loginAction(){
        $request = $this->getRequest();
        $session = $request->getSession();

        if($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)){
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        }
        return $this->render(
            'TriviaMessengerBundle:Messenger:login.html.twig',
            array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $error,
            )
        );
    }
    public function registerAction(Request $request){
        $user = new Users();
        $user->setUsername('Your username here');
        $user->setEmail('Your email here');
        $user->setPassword('Password here');
        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'password')
            ->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $automessage = new Messages();
                $automessage->setRecipient($user->getUsername());
                $automessage->setName('System');
                $automessage->setText('Welcome to our neat little messaging system, ' . $user->getUsername() );
                $em = $this->getDoctrine()->getManager();
                $em->persist($user, $automessage);
                $em->flush();
                $this->($user);
                return $this->redirect($this->generateUrl('messages'));
            }
        }
        return $this->render('TriviaMessengerBundle:Messenger:register.html.twig', array('form' => $form->createView(),));
    }

    public function profileAction(Request $request){
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('email', 'email')
            ->getForm();
        if($request->isMethod('POST')){
            $form->bind($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('profile'));
        }
        return $this->render('TriviaMessengerBundle:Messenger:profile.html.twig', array('form' => $form->createView(),));
    }

}