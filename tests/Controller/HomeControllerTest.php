<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    private const HOMPAGE_ROUTE =  "route_homepage";

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    
    public function UrlGenerator(){
        return $this->client->getContainer()->get('router');
    }
    
    public function test_HomePageRoute(): void
    {
        $this->client->request('GET', $this->UrlGenerator()->generate(self::HOMPAGE_ROUTE));
        $this->assertResponseIsSuccessful();
    }

    public function test_UploadForm(): void
    {
        $crawler = $this->client->request('GET', $this->UrlGenerator()->generate(self::HOMPAGE_ROUTE));

        $filesField = $crawler->filter('form[name=upload_form]')
            ->filter('input[type=file]')
            ->matches('input[type=file]');
        $this->assertTrue($filesField);
        
        $tokenField = $crawler->filter('form[name=upload_form]')
            ->filter('input[type=hidden]')
            ->matches('input[type=hidden]');
        $this->assertTrue($tokenField);
    }
}