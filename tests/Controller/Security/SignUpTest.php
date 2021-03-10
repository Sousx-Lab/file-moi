<?php

namespace App\Tests\Controller\Security;


use App\Entity\Auth\User;
use App\Tests\Controller\NeedLogin;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

final class SignUpTest extends WebTestCase
{

    private const REGISTRATION_ROUTE = "route_registration";

    private KernelBrowser $client;

    private EntityManagerInterface $em;

    use FixturesTrait;

    use NeedLogin;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $purger = new ORMPurger($this->em);
        $purger->purge();
    }

    public function UrlGenerator()
    {
        return $this->client->getContainer()->get('router');
    }

    public function test_RegistrationPageRoute(): void
    {
        $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));

        $this->assertResponseIsSuccessful();
    }

    public function getRegistrationForm(): Crawler
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));
        return $crawler->filter('form[name=registration]');
    }

    public function test_RegistrationFormFields(): void
    {
        $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));

        $form = $this->getRegistrationForm()->matches('form[name=registration]');
        $this->assertTrue($form);

        $emailField = $this->getRegistrationForm()
            ->filter('input[type=email]')
            ->matches('input[type=email]');
        $this->assertTrue($emailField);

        $passwordField = $this->getRegistrationForm()
            ->filter('input[type=password]')
            ->matches('input[type=password]');
        $this->assertTrue($passwordField);

        $confirmPasswordField = $this->getRegistrationForm()
            ->filter('input[type=password]')
            ->matches('input[type=password]');
        $this->assertTrue($confirmPasswordField);

        $csrfTokenField = $this->getRegistrationForm()
            ->filter('input[name=token]')
            ->matches('input[name=token]');
        $this->assertTrue($csrfTokenField);
    }

    public function test_RedirectIfUserAlreadyLogged(): void
    {

        $user = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);
        $this->login($this->client, $user['user_user']);

        $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));
        $this->assertResponseRedirects("/");
        $this->client->followRedirect();
        $this->assertRegExp('/You are already logged in as much as : John1/', $this->client->getResponse()->getContent());
    }

    public function test_UserNotificationMessageDispatched(): void
    {

        $form = $this->getRegistrationForm()->selectButton('Sign in up')
            ->form([
                'email' => 'john@doe.fr',
                'password' => 'Password1',
                'confirmPassword' => 'Password1'
            ], 'POST');
        $this->client->submit($form);
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'john@doe.fr']);

        $this->assertInstanceOf(User::class, $user);

        /**@var InMemoryTransport */
        $transport = self::$container->get('messenger.transport.async');

        $this->assertCount(1, $transport->get());
    }
}
