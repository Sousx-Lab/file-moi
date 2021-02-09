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
}