<?php
namespace App\Tests\Controller\Security;

use App\Tests\Controller\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SignInUpTest extends WebTestCase 
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

    public function test_RegistrationFormFields():void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::REGISTRATION_ROUTE));

        $form = $crawler->filter('form[name=registration]')->matches('form[name=registration]');
        $this->assertTrue($form);

        $emailField = $crawler->filter('form[name=registration]')
            ->filter('input[type=email]')
            ->matches('input[type=email]');
        $this->assertTrue($emailField);

        $passwordField = $crawler->filter('form[name=registration]')
            ->filter('input[type=password]')
            ->matches('input[type=password]');
        $this->assertTrue($passwordField);
        
        $confirmPasswordField = $crawler->filter('form[name=registration]')
            ->filter('input[type=password]')
            ->matches('input[type=password]');
        $this->assertTrue($confirmPasswordField);

        $csrfTokenField = $crawler->filter('form[name=registration]')
            ->filter('input[name="registration[_token]" ]')
            ->matches('input[name="registration[_token]" ]');
        $this->assertTrue($csrfTokenField);

        $formElem = $crawler->filter('form[name=registration]')
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