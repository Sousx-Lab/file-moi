<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    
    public function test_HomePageRoute_ShouldReturnSuccesfullResponse(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
}