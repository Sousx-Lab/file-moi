<?php

use App\Entity\Auth\User;
use App\Tests\Controller\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FilesManagerControllerTest extends WebTestCase
{
    use NeedLogin;
    use FixturesTrait;
    
    private const MY_FILES_ROUTE = "route_files_manager";
    
    private const LOGIN_ROUTE = "route_login";
    
    private static ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        if (null === self::$client) {
            self::$client = static::createClient();
        }
    }

    public function UrlGenerator(string $route, array $params = [], int $path = 1 ): string
    {
        /**@var UrlGeneratorInterface */
        $router = self::$client->getContainer()->get('router');
        return $router->generate($route, $params, $path);
    }

    public function test_ForbiddenRedirectToLogin(): void
    {    
        self::$client->request('GET', $this->UrlGenerator(self::MY_FILES_ROUTE));
        self::$client->followRedirects();
        $this->assertResponseRedirects($this->UrlGenerator(self::LOGIN_ROUTE));
    }

    public function test_FilesManagerWithUser(): void
    {
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        /**@var User */
        $user = $fixtures['user_has_file'];

        $this->login(self::$client, $user);
        self::$client->request('GET', $this->UrlGenerator(self::MY_FILES_ROUTE));

        $this->assertResponseIsSuccessful();
        $this->assertMatchesRegularExpression('/My files/', self::$client->getResponse()->getContent());

        $files = $user->getFiles()->getValues();
        foreach ($files as $file) {
            $this->assertMatchesRegularExpression("/{$file->getFileName()}/", self::$client->getResponse()->getContent());
        }
        
    }


}