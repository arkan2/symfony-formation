<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testCreateUserAccount()
    {
        $client = static::createClient();

        // 1. Go to the front page
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        // 2. Go to the registration page
        $crawler = $client->getCrawler();
        $crawler = $client->click($crawler->selectLink("S'inscrire")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());

        // 3. Try to create a user account with an existing username
        $form = $crawler->selectButton('Créer mon compte')->form();
        $crawler = $client->submit($form, [
            'app_registration' => [
                'username' => 'jsmith',
                'fullName' => 'John Smith',
                'emailAddress' => 'john@smith.com',
                'password' => [
                    'first' => 'qwertyuiop',
                    'second' => 'qwertyuiop',
                ],
                'birthdate' => [
                    'day' => '20',
                    'month' => '02',
                    'year' => '1980',
                ],
            ],
        ]);

        $this->assertSame(
            'user.username.already_exists',
            trim($crawler->filter('#app_registration tr:first-child > td > ul li:first-child')->text())
        );

        // 4. Try to create a new valid user account
        $form = $crawler->selectButton('Créer mon compte')->form();
        $crawler = $client->submit($form, [
            'app_registration' => [
                'username' => 'jsmith88',
                'fullName' => 'John Smith',
                'emailAddress' => 'john@smith88.com',
                'password' => [
                    'first' => 'qwertyuiop',
                    'second' => 'qwertyuiop',
                ],
                'birthdate' => [
                    'day' => '20',
                    'month' => '2',
                    'year' => '1980',
                ],
            ],
        ]);

        $container = $client->getContainer();
        $manager = $container->get('app.user_manager');

        $this->assertInstanceOf(
            'AppBundle\User\User',
            $manager->findByUsername('jsmith88')
        );

        // 5. Redirect to the login page
        $this->assertTrue($client->getResponse()->isRedirect('/fr/login'));

        $client->followRedirect();

        @unlink($container->getParameter('database_path').'/jsmith88.txt');
    }
}
