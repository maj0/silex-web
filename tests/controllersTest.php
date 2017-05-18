<?php

use Silex\WebTestCase;

class controllersTest extends WebTestCase
{
    public function testGetHomepage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('Welcome', $crawler->filter('body')->text());
    }

    public function testGetAboutpage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/about');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('Welcome', $crawler->filter('body')->text());
    }

    public function testGetUserPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('home', $crawler->filter('body')->text());

    }

    public function testGetUserAddPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/add');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doAdd', $crawler->filter('body')->text());
    }

    public function testGetUserEditPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/edit/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doEdit', $crawler->filter('body')->text());
    }

    public function testGetUserDeletePage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/delete/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doDelete', $crawler->filter('body')->text());
    }

    public function testGetUserShowPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/show/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doShow', $crawler->filter('body')->text());
    }

    public function testGetOrganisationPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('home', $crawler->filter('body')->text());
    }

    public function testGetOrganisationAddPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/add');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doAdd', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEditPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/edit/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doEdit', $crawler->filter('body')->text());
    }

    public function testGetOrganisationShowPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/show/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doShow', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEmployeePage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/1/employee');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('home', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEmployeeAddPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/1/employee/add');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doAdd', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEmployeeEditPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/1/employee/edit/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doEdit', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEmployeeShowPage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/1/employee/show/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doShow', $crawler->filter('body')->text());
    }

    public function testGetOrganisationEmployeeDeletePage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/1/employee/delete/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doDelete', $crawler->filter('body')->text());
    }

 
    public function testGetOrganisationDeletePage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/organisation/delete/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('doDelete', $crawler->filter('body')->text());
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';
        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../src/controllers.php';
        $app['session.test'] = true;

        return $this->app = $app;
    }

}
