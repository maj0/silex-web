<?php

namespace Tests;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;

class EmployerControllerTest extends WebTestCase
{
    private $client = null;
    private $loggedIn = false;

    public function testSecuredHome()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testUnsecuredContact()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/contact');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testUnsecuredAbout()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/about');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredUser()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredUserAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/add');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredUserEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/edit/7');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredUserShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/show/7');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredUserDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        $crawler = $client->request('GET', '/user/delete/7');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisation()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredOrganisationAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/add');
        

        $this->assertNotSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredOrganisationEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/edit/1');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisationShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/show/1');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisationDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/delete/1');
        

        $this->assertNotSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisationEmployeeAdd()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/1/employee/add');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisationEmployeeEdit()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/1/employee/edit/7');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }

    public function testSecuredOrganisationEmployeeShow()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/1/employee/show/7');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    public function testSecuredOrganisationEmployeeDelete()
    {
        //error_log("IN ".__FUNCTION__);
        $client = $this->createClient();
        $client->followRedirects(true);
        $this->logIn($client);
        
        $crawler = $client->request('GET', '/organisation/1/employee/delete/7');
        

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
    }


    private function logIn($client)
    {
        $username = 'employer';
        //$session = $this->client->getContainer()->get('session');
        $session = $this->app['session'];
        if (empty($this->loggedIn)) {
            $roles = array('ROLE_ADMIN');

            // the firewall context defaults to the firewall name
            $firewallContext = 'secured';
            
            $token = new UsernamePasswordToken($username, null, $firewallContext, $roles);
            $user = new User($username, null, $roles, true, true, true, true);
            $token->setUser($user);
            $session->set('_security_'.$firewallContext, serialize($token));
            $session->save();
            $this->loggedIn = true;
        }
        
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        
        return $client;
    }
    
    public function createApplication()
    {
        static $app;
        if (empty($app)) {
            $app = require __DIR__.'/../src/app.php';
            $session = $this->app['session'];
            require __DIR__.'/../src/routes.php';
            if (empty($app['session.test'])) {
                $app['session.storage'] = function () {
                    return new MockArraySessionStorage();
                };
                $app['session.test'] = true;
            }
            $app['debug'] = true;
        }

        return $this->app = $app;
    }
}
