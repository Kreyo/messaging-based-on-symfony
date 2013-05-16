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
        if ( !isset($_REQUEST['term']) )
            exit;
        $results = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('user')
            ->from('TriviaMessengerBundle:Users', 'user')
            ->where('user.username like :term')
            ->setParameter('term',$this->getRequest()->get('term') )
            ->getQuery()
            ->getResult();
        $suggestions=array();


            foreach($results as $user)
            {
                $suggestions = array(
                    'label' => $user->getId(),
                    'value' => $user->getUsername(),
                );


            }


// jQuery wants JSON data
        echo json_encode($suggestions);
        flush();
        return new JsonResponse(array('suggestions'=>$suggestions));
    }

}
