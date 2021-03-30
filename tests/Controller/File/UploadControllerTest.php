<?php

namespace App\Tests\Controller\File;

use App\Tests\Controller\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UploadControllerTest extends WebTestCase
{
    use FixturesTrait;
    use NeedLogin;
    use FileGenerator;

    private const UPLOAD_ROUTE =  "route_file_upload";

    private KernelBrowser $client;

    private FileGenerator $fileGenerator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanFiles();
    }

    public function UrlGenerator(string $route, array $params = [], int $path = 1 ): string
    {
        /**@var UrlGeneratorInterface */
        $router = $this->client->getContainer()->get('router');
        return $router->generate($route, $params, $path);
    }

    public function test_BadHttpMethod(): void
    {
        $this->client->request('GET', $this->UrlGenerator(self::UPLOAD_ROUTE));
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function test_BadCsrfToken(): void
    {

        $this->client->request(
            'POST',
            $this->UrlGenerator(self::UPLOAD_ROUTE),
            [
                '_token' => "12456789"
            ],
            [
                'files' => $this->createFiles(1)
            ],
            [
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ],

        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_EmptyUploadedFile(): void
    {
        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('upload');
        $this->client->request(
            'POST',
            $this->UrlGenerator(self::UPLOAD_ROUTE),
            [
                '_token' => $csrfToken
            ],
            [
                'files' => $this->createFiles(0)
            ],
            [
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ],

        );
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function test_UploadMultipleFiles(): void
    {   

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('upload');
        $crawler = $this->client->request(
            'POST',
            $this->UrlGenerator(self::UPLOAD_ROUTE),
            [
                '_token' => $csrfToken
            ],
            [
                'files' => $this->createFiles(2)
            ],
            [
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ],

        );
        $ldFileUrl = $crawler->selectLink('Download')->count();
        $this->assertEquals(2, $ldFileUrl);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
