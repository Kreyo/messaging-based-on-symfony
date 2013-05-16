<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SystemController extends Controller
{
    public function redirectAction(){
        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
    }

    public function autocompleteAction(){
        if ( !isset($_REQUEST['term']) )
            exit;
        $rs = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('user')
            ->from('TriviaMessengerBundle:Users', 'user')
            ->where('user like :term')
            ->setParameter('term',$_REQUEST['term'] )
            ->getQuery()
            ->getResult();
        if ( $rs && mysql_num_rows($rs) )
        {
            while( $row = mysql_fetch_array($rs, MYSQL_ASSOC) )
            {
                $data[] = array(
                    'label' => $row['Id'],
                    'value' => $row['Username']
                );
            }
        }

// jQuery wants JSON data
        echo json_encode($data);
        flush();
        return $data;
    }

}
