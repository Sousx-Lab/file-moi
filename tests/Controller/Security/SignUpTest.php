<?php
namespace App\Tests\Controller\Security;

use App\Tests\Controller\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SignUpTest extends WebTestCase 
{

    private const REGISTRATION_ROUTE = "route_registration";
    private KernelBrowser $client;
    
    use FixturesTrait;
    use NeedLogin;

    protected function setUp(): void
    {
        $this->client = static::createClient();
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

    public function getRegistrationForm()
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));
        return $crawler->filter('form[name=registration]');
    }

    public function test_RegistrationFormFields():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));

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
            ->filter('input[name="registration[_token]" ]')
            ->matches('input[name="registration[_token]" ]');
        $this->assertTrue($csrfTokenField);

        $formElem = $this->getRegistrationForm()
                ->filterXPath('//input[contains(@name, "registration")]')->evaluate('substring-after(@name, "registration")');

        $this->assertTrue($formElem === ['[email]', '[password]', '[confirmPassword]', '[_token]']);
    }

    public function test_RedirectIfUserAlreadyLogged():void
    {
        
        $user = $this->loadFixtureFiles([dirname(__DIR__, 1). '/users.yaml']);
        $this->login($this->client, $user['user_user']);
    
        $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));
        $this->assertResponseRedirects("/");
        $this->client->followRedirect();
        $this->assertRegExp('/You are already logged in as much as : John1/', $this->client->getResponse()->getContent());
       
    }
}