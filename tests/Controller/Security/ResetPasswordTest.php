<?php

namespace App\Tests\Controller\Security;

use App\Entity\Auth\User;
use Symfony\Component\DomCrawler\Crawler;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResetPasswordTest extends WebTestCase
{
    use FixturesTrait;
    private const RESET_PASS_ROUTE = "route_password_reset";
    private const LOGIN_ROUTE =  "route_login";

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function UrlGenerator(): UrlGeneratorInterface
    {
        return $this->client->getContainer()->get('router');
    }

    public function getPassworResetForm(): Crawler
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::RESET_PASS_ROUTE));
        return $crawler->filter('form[name=password_reset_request]');
    
    }

    public function test_ForgotPasswordRoute(): void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::LOGIN_ROUTE));

        $link = $crawler->selectLink('Forgot password')->link();
        $this->client->click($link);
        $this->client->followRedirects();
        $this->assertResponseIsSuccessful();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/password/reset');
    }

    public function test_PasswordResetFormEmailField(): void
    {
        $emailField = $this->getPassworResetForm()->filter('form[name=password_reset_request]')
            ->filter('input[name=email]')
            ->matches('input[type=email]');
        $this->assertTrue($emailField);
    }

    public function test_PasswordResetBadEmail(): void
    {
        $form = $this->getPassworResetForm()->selectButton('Recovery')
            ->form([
                'email' => "fake@email.com"
            ]);

        $this->client->submit($form);
        $this->assertRegExp('/User not found./', $this->client->getResponse()->getContent());
    }

    public function test_OngoingPasswordReset(): void
    {
        
        $fixtures= $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        /**@var User */
        $user = $fixtures['user_password_reset'];

        $form = $this->getPassworResetForm()->selectButton('Recovery')
            ->form([
                'email' => $user->getEmail(),
            ]);
        $this->client->submit($form);
        $this->assertRegExp('/Ongoing password reset./', $this->client->getResponse()->getContent());
    }

    public function test_PasswordResetGoodEmail(): void
    {
        /**@var User */
        $user = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        $form = $this->getPassworResetForm()->selectButton('Recovery')
            ->form([
                'email' => $user['user_user']->getEmail(),
            ]);
        $this->client->submit($form);

        $this->assertResponseRedirects("/login");
        $this->client->followRedirect();

    }
}
