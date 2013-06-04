<?php

namespace Trivia\MessengerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessengerControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        // let's register the 1st bot

        $botname1 = sha1(rand(27, 93). time());
        $botname2 = sha1(rand(27, 93). time(). 'bot');
        $botpassword1 = sha1(rand(27, 93). time());
        $botpassword2 = sha1(rand(27, 93). time());

        $form = $crawler->selectButton('submit')->form();
        $form['username'] = $botname1;
        $form['email'] = 'test@test.com';
        $form['password.first'] = $botpassword1;
        $form['password.second'] = $botpassword1;

        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        // was there a redirect? okay, let's register the 2nd bot

        $crawler = $client->request('GET', '/logout');
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('submit')->form();
        $form['username'] = $botname2;
        $form['email'] = 'test2@test.com';
        $form['password.first'] = $botpassword2;
        $form['password.second'] = $botpassword2;
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        // second redirect? perfect. let's send a message!

        $crawler = $client->request('GET', '/messages/create');
        // did the bot get to the /create page?
        $this->assertTrue($crawler->filter( 'html:contains("Recipient")')->count());
        $form = $crawler->selectButton('submit')->form();
        $form['Recipient'] = $botname1;
        $form['text'] = 'Heyheyhey, we are both robots!';
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

    }
}
