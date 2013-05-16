<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class SystemController extends Controller
{
    public function redirectAction(){
        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
    }

    public function autocompleteAction(){
        if (!$this->getRequest()->get('term')) {
            return new JsonResponse(array());
        }

        $results = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('user')
            ->from('TriviaMessengerBundle:Users', 'user')
            ->where('user.username LIKE :term')
            ->setParameter('term', $this->getRequest()->get('term') .'%' )
            ->getQuery()
            ->getResult();

        $suggestions = array();


        foreach($results as $user)  {
            $suggestions[] = array(
                'label' => $user->getId(),
                'value' => $user->getUsername(),
            );
        }

        return new JsonResponse($suggestions);
    }
}
