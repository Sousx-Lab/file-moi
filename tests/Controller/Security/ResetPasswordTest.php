<?php

namespace App\Tests\Controller\Security;

use App\Entity\Auth\User;
use Symfony\Component\DomCrawler\Crawler;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\Entity\Auth\Password\PasswordResetToken;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResetPasswordTest extends WebTestCase
{
    use FixturesTrait;

    private const RESET_PASS_ROUTE = "route_password_reset";

    private const UPDATE_PASS_ROUTE = "route_update_password";

    private const LOGIN_ROUTE =  "route_login";

    private static ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        if (null === self::$client)
            self::$client = static::createClient();
    }

    public function UrlGenerator(string $route): string
    {
        /**@var UrlGeneratorInterface */
        $router = self::$client->getContainer()->get('router');
        return $router->generate($route);
    }

    public function getPassworResetForm(): Crawler
    {
        $crawler = self::$client->request('GET', $this->UrlGenerator(self::RESET_PASS_ROUTE));
        return $crawler->filter('form[name=password_reset_form]');
    }

    public function test_ForgotPasswordRoute(): void
    {
        $crawler = self::$client->request('GET', $this->UrlGenerator(self::LOGIN_ROUTE));

        $link = $crawler->selectLink('Forgot password')->link();
        self::$client->click($link);
        self::$client->followRedirects();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::$client->getRequest()->getPathInfo(), '/password/reset');
    }

    public function test_PasswordResetFormEmailField(): void
    {
        $emailField = $this->getPassworResetForm()->filter('form[name=password_reset_form]')
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

        self::$client->submit($form);
        $this->assertMatchesRegularExpression('/User not found./', self::$client->getResponse()->getContent());
    }

    public function test_OngoingPasswordReset(): void
    {

        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        /**@var User */
        $user = $fixtures['user_password_reset'];

        $form = $this->getPassworResetForm()->selectButton('Recovery')
            ->form([
                'email' => $user->getEmail(),
            ]);
        self::$client->submit($form);
        $this->assertMatchesRegularExpression('/Ongoing password reset./', self::$client->getResponse()->getContent());
    }

    public function test_PasswordResetGoodEmail(): void
    {
        /**@var User */
        $user = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        $form = $this->getPassworResetForm()->selectButton('Recovery')
            ->form([
                'email' => $user['user_user']->getEmail(),
            ]);
        self::$client->submit($form);

        $this->assertResponseRedirects($this->UrlGenerator(self::LOGIN_ROUTE));
        self::$client->followRedirect();
    }

    public function test_UpdatePasswordWithBadTokenOrUserId(): void
    {
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);
        /**@var User */
        $user = $fixtures['user_password_reset'];

        /**@var PasswordResetToken */
        $token = $fixtures['token_token'];

        self::$client->request('GET', $this->UrlGenerator(
            self::UPDATE_PASS_ROUTE,
            [
                'id' => $user->getId(),
                'token' => '123456789'
            ]
        ));
        $this->assertResponseRedirects('/login');
        self::$client->followRedirect();
        $this->assertStringContainsString('This token is expired', self::$client->getResponse()->getContent());

        self::$client->request('GET', $this->UrlGenerator(
            self::UPDATE_PASS_ROUTE,
            [
                'id' => 'a73d1c0d-18eb-4e2a-9927-2dfa26802df2',
                'token' => $token->getToken()

            ]
        ));
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_UpdatePasswordWithGoodTokenAndUserId(): void
    {
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);
        /**@var User */
        $user = $fixtures['user_password_reset'];

        /**@var PasswordResetToken */
        $token = $fixtures['token_token'];

        $crawler = self::$client->request('GET', $this->UrlGenerator(
            self::UPDATE_PASS_ROUTE,
            [
                'id' => $user->getId(),
                'token' => $token->getToken()
            ]
        ));

        $form = $crawler->filter('form[name=update_password_request]')->selectButton('Update')
            ->form([
                'password' => 'NewPassword123',
                'confirmPassword' => 'NewPassword123'
            ]);

        self::$client->submit($form);
        $this->assertResponseRedirects($this->UrlGenerator(self::LOGIN_ROUTE));
        self::$client->followRedirect();
        $this->assertStringContainsString('Your password has been updated successfuly', self::$client->getResponse()->getContent());
    }
}
