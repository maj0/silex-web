<?php

namespace Tests;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;

class AdminControllerTest extends WebTestCase
{
    private $client = null;
    private $loggedIn = false;


    public function testSecuredHome()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testUnsecuredContact()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        //$this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/contact');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testUnsecuredAbout()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        //$this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/about');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredUser()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredUserAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/add');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredUserEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/edit/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredUserShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/show/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredUserDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/delete/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisation()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredOrganisationAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/add');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredOrganisationEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/edit/1');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisationShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/show/1');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisationDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/delete/1');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisationEmployeeAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/1/employee/add');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisationEmployeeEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/1/employee/edit/7');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredOrganisationEmployeeShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/1/employee/show/7');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }


    public function testSecuredOrganisationEmployeeDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/organisation/1/employee/delete/7');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    public function testSecuredLogout()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        //file_put_contents('temp.txt', print_r($this->client, 1));
        $crawler = $client->request('GET', '/admin/logout');
        //file_put_contents('user_h1.txt', $crawler->text());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //$this->assertSame('Admin Dashboard', $crawler->filter('h1')->text());
    }

    private function logIn($client)
    {
        $username = 'admin';
        //$session = $this->client->getContainer()->get('session');
        $session = $this->app['session'];
        if (empty($this->app['security.token_storage'])) {
            $roles = array('ROLE_ADMIN');

            // the firewall context defaults to the firewall name
            $firewallContext = 'secured';
            
            $token = new UsernamePasswordToken($username, null, $firewallContext, $roles);
            $user = new User($username, null, $roles, true, true, true, true);
            $token->setUser($user);
            $session->set('_security_'.$firewallContext, serialize($token));
            $session->set('username', $username);
            $session->save();
        }
        
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        
        $this->loggedIn = true;
        return $client;
    }
    
    public function createApplication()
    {
        static $app;
        if (empty($app)) {
            $app = require __DIR__.'/../src/app.php';
            $session = $this->app['session'];
            //file_put_contents(__CLASS__ . 'app.txt', print_r($app,1));
            unset($this->app['security.token_storage']);
            //require __DIR__.'/../config/dev.php';
            require __DIR__.'/../src/routes.php';
            $app['session.storage'] = function () {
                return new MockArraySessionStorage();
            };
            $app['session.test'] = true;
            $app['debug'] = true;
        }

        return $this->app = $app;
    }
}
