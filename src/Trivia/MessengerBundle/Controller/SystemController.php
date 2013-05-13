<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SystemController extends Controller
{
    public function redirectAction(){
        return $this->redirect($this->generateUrl('trivia_messenger_homepage'));
    }

}
