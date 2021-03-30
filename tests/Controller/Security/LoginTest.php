<?php

namespace App\Tests\Controller\Security;

use App\Tests\Controller\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LoginTest extends WebTestCase
{
    private const LOGIN_ROUTE =  "route_login";
    private const LOGOUT_ROUTE = "route_logout";

    use FixturesTrait;
    use NeedLogin;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function UrlGenerator(string $route): string
    {
        /**@var UrlGeneratorInterface */
        $router = $this->client->getContainer()->get('router');
        return $router->generate($route);
    }

    public function test_LoginPageRoute(): void
    {
        $this->client->request('GET', $this->urlGenerator(self::LOGIN_ROUTE));
        $this->assertResponseIsSuccessful();
    }

    public function test_LoginFormFields(): void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator(self::LOGIN_ROUTE));
        
        $emailField = $crawler->filter('form[name=login_form]')
            ->filter('input[type=email]')
            ->matches('input[type=email]');
        $this->assertTrue($emailField);

        $passwordField = $crawler->filter('form[name=login_form]')
            ->filter('input[type=password]')
            ->matches('input[type=password]');
        $this->assertTrue($passwordField);
        
        $csrfField = $crawler->filter('form[name=login_form]')
            ->filter('input[name=_csrf_token]')
            ->matches('input[name=_csrf_token]');
        $this->assertTrue($csrfField);
    }

    public function test_TryLoginWithoutCredentials(): void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator(self::LOGIN_ROUTE));
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => " ",
                'password' => " ",
            ]);
        $this->client->submit($form);
        
        $this->assertResponseRedirects($this->UrlGenerator(self::LOGIN_ROUTE));
        $this->client->followRedirect();
        $this->assertRegExp('/Invalid credentials./', $this->client->getResponse()->getContent());
    }

    public function test_TryLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator(self::LOGIN_ROUTE));
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => "emailTest@email.fr",
                'password' => "PasswordTest",
            ]);
        $this->client->submit($form);

        $this->assertResponseRedirects($this->UrlGenerator(self::LOGIN_ROUTE));
        $this->client->followRedirect();
        $this->assertRegExp('/Invalid credentials./', $this->client->getResponse()->getContent());
    }

    public function test_LoginWhitGoodCredentials(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);
        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->client->request('POST', $this->UrlGenerator(self::LOGIN_ROUTE), [
            'csrf_token' => $csrfToken,
            'email' => 'john@doe.fr',
            'password' => '0000',
        ]);
        $this->assertResponseRedirects("");
        $this->client->followRedirect();

        $this->assertRegExp('/john@doe.fr/', $this->client->getResponse()->getContent());
    }

    public function test_LogoutRoute(): void
    {
        $user = $this->loadFixtureFiles([dirname(__DIR__, 1). '/users.yaml']);
        $this->login($this->client, $user['user_user']);
        $this->client->request('GET', $this->UrlGenerator(self::LOGOUT_ROUTE));

        $this->assertResponseRedirects();
        $this->assertNull($this->client->getContainer()->get('security.token_storage')->getToken());
    }

}
