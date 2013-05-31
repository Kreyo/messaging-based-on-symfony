<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trivia\MessengerBundle\Entity\Users;
use Symfony\Component\Security\Core\SecurityContext;
use Trivia\MessengerBundle\Entity\Messages;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\FormError;
class UserController extends Controller
{
    public function loginAction()
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

        return $this->render(
            'TriviaMessengerBundle:Messenger:login.html.twig',
            array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $error,
            )
        );
    }

    public function registerAction(Request $request)
    {
        $user = new Users();

        $form = $this->createFormBuilder()
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Password fields must be equal! Hail the equality!',

        ))
            ->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $formData = $form->getData();
            $user->setUsername($formData['username']);
            $user->setEmail($formData['email']);
            $user->setPassword($formData['password']);
            $user->setEmailToken(sha1(rand(42, 4711) . time()));
            if ($form->isValid() && $this->getDoctrine()->getRepository('TriviaMessengerBundle:Users')->findOneByUsername($formData['username'])==null) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $automessage = new Messages();
                $automessage->setToUser($user);

                $automessage->setFromUser(null);
                $automessage->setText('Welcome to our neat little messaging system, ' . $user->getUsername() );
                $automessage->setUnread();
                $em->persist($automessage);
                $em->flush();

                $emailmessage = \Swift_Message::newInstance()
                    ->setSubject("Hey there, sailor")
                    ->setFrom($this->container->getParameter('mailer_from'))
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('TriviaMessengerBundle:Messenger:email.html.twig',
                    array('username' => $user->getUsername(),
                        'emailToken' => $user->getEmailToken())
                ),'text/html')
                    ->addPart($this->renderView('TriviaMessengerBundle:Messenger:email.txt.twig',
                    array('username' => $user->getUsername(),
                        'emailToken' => $user->getEmailToken())
                ), 'text/plain');
                $this->get('mailer')->send($emailmessage);
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
                $this->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
            } else $form->get('username')->addError(new FormError('Well, this is awkward. Such username already exists!'));
        }

        return $this->render('TriviaMessengerBundle:Messenger:register.html.twig', array('form' => $form->createView(),));
    }

    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('email', 'email')
            ->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('trivia_messenger_profile'));
        }

        return $this->render('TriviaMessengerBundle:Messenger:profile.html.twig', array('form' => $form->createView(),));
    }
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
    }

    public function verifyAction($token)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('TriviaMessengerBundle:Users')->findOneByEmailToken($this->getRequest()->get('token'));
        $user->setEmailToken(null);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));

    }

    public function resendAction()
    {
        $user=$this->getUser();
        $emailmessage = \Swift_Message::newInstance()
            ->setSubject("Hey there, sailor")
            ->setFrom($this->container->getParameter('mailer_from'))
            ->setTo($user->getEmail())
            ->setBody($this->renderView('TriviaMessengerBundle:Messenger:email.html.twig',
                  array('username' => $user->getUsername(),
                        'emailToken' => $user->getEmailToken())
            ),'text/html')
            ->addPart($this->renderView('TriviaMessengerBundle:Messenger:email.txt.twig',
                  array('username' => $user->getUsername(),
                        'emailToken' => $user->getEmailToken())
            ), 'text/plain');
        $this->get('mailer')->send($emailmessage);
        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));

    }

}
