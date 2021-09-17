<?php
namespace App\Tests\Controller;

use App\Tests\Controller\File\FileGenerator;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

final class HomeControllerTest extends WebTestCase
{
    use FixturesTrait;
    use NeedLogin;
    use FileGenerator;

    private const HOMPAGE_ROUTE =  "route_homepage";

    private static ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        if (null === self::$client) {
            self::$client = static::createClient();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanFiles();
    }

    public function UrlGenerator(string $route, ?array $params = [], ?int $path = 1): string
    {    
        $router = $this->bootKernel()->getContainer()->get('router');
        return $router->generate($route, $params, $path);
    }
    
    public function test_HomePageRoute(): void
    {
        self::$client->request('GET', $this->UrlGenerator(self::HOMPAGE_ROUTE));
        $this->assertResponseIsSuccessful();
    }

    public function test_UploadForm(): void
    {
        $crawler = self::$client->request('GET', $this->UrlGenerator(self::HOMPAGE_ROUTE));

        $filesField = $crawler->filter('form[name=upload_form]')
            ->filter('input[type=file]')
            ->matches('input[type=file]');
        $this->assertTrue($filesField);
        
        $tokenField = $crawler->filter('input[name=_token]')
            ->matches('[type=hidden]');
        $this->assertTrue($tokenField);
    }

    public function test_BadCsrfToken(): void
    {

        $crawler = self::$client->request('GET', $this->UrlGenerator(self::HOMPAGE_ROUTE));

        $form = $crawler->selectButton('Upload files...')->form();
        
        $node = $crawler->filter("input[name='files[]']")->getNode(0);
        $newField = new FileFormField($node);
        
        $form->set($newField);
        $crawler = self::$client->submit($form, [
            'files' => $this->createFiles(1),
            '_token' => '1231456789'
        ]);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, self::$client->getResponse()->getStatusCode());
    }

    public function test_EmptyUploadedFile(): void
    {
        $crawler = self::$client->request('GET', $this->UrlGenerator(self::HOMPAGE_ROUTE));

        $form = $crawler->selectButton('Upload files...')->form();
        
        $node = $crawler->filter("input[name='files[]']")->getNode(0);
        $newField = new FileFormField($node);
        
        $form->set($newField);
        $crawler = self::$client->submit($form, [
            'files' => [],
        ]);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, self::$client->getResponse()->getStatusCode());
    }

    public function test_UploadMultipleFiles(): void
    {   
        $crawler = self::$client->request('GET', $this->UrlGenerator(self::HOMPAGE_ROUTE));

        $form = $crawler->selectButton('Upload files...')->form();
        
        $node = $crawler->filter("input[name='files[]']")->getNode(0);
        $newField = new FileFormField($node);
        
        $form->set($newField);
        $crawler = self::$client->submit($form, [
            'files' => $this->createFiles(2),
        ]);
        
        $dlFileUrl = $crawler->selectLink('Download')->count();
        
        $this->assertEquals(2, $dlFileUrl);
        $this->assertEquals(Response::HTTP_SEE_OTHER, self::$client->getResponse()->getStatusCode());
    }
}