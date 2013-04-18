<?php

namespace Trivia\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessagesController extends Controller
{
    public function indexAction()
    {
        return $this->render('TriviaMessengerBundle:Messenger:index.html.twig');
    }

}
