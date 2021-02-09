<?php

namespace App\Tests\Controller\Security;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityControllerTest extends WebTestCase
{
    private const LOGIN_ROUTE =  "route_login";
    use FixturesTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function UrlGenerator(){
        return $this->client->getContainer()->get('router');
    }

    public function test_LoginPageRoute() :void
    {
        $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function test_EmaildFieldForm():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $this->assertSelectorExists('form');
        $emailField = $crawler->filter('input[type=email]')->matches('input[type=email]');
        $this->assertTrue($emailField);
    }

    public function test_PasswordFieldForm():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $this->assertSelectorExists('form');
        $passwordField = $crawler->filter('input[type=password]')->matches('input[type=password]');
        $this->assertTrue($passwordField);
    }

    public function test_CsrfdFieldForm():void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertSelectorExists('form');
        $csrfField = $crawler->filter('input[type=hidden]')->matches('input[type=hidden]');
        $this->assertTrue($csrfField);
    }


    public function test_TryLoginWithoutCredentials():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => " ",
                'password' => " ",
            ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $this->client->followRedirect();
        $this->assertRegExp('/Identifiants invalides./', $this->client->getResponse()->getContent());
    }

    public function test_TryLoginWithBadCredentials():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => "emailTest@email.fr",
                'password' => "PasswordTest",
            ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->UrlGenerator()->generate(self::LOGIN_ROUTE));
        $this->client->followRedirect();
        $this->assertRegExp('/Identifiants invalides./', $this->client->getResponse()->getContent());
    }

    public function test_LoginWhitGoodCredentials():void
    {
        $this->loadFixtureFiles([dirname(__DIR__, 1). '/users.yaml']);
        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->client->request('POST', '/login',[
            'csrf_token' => $csrfToken,
            'email' => 'john@doe.fr',
            'password' => '0000',
        ]);
        $this->assertResponseRedirects("");
        $this->client->followRedirect();
        $this->assertRegExp('/john@doe.fr/', $this->client->getResponse()->getContent());
    }

}