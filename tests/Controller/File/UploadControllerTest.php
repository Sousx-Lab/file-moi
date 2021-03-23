<?php

namespace App\Tests\Controller\File;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UploadControllerTest extends WebTestCase
{
    private const UPLOAD_ROUTE =  "route_file_upload";

    private KernelBrowser $client;

    private FileGenerator $fileGenerator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->fileGenerator = new FileGenerator();
    }

    public function UrlGenerator(string $route): string
    {
        /**@var UrlGeneratorInterface */
        $router = $this->client->getContainer()->get('router');
        return $router->generate($route);
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
                'files' => $this->fileGenerator->createFiles(1)
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
                'files' => $this->fileGenerator->createFiles(0)
            ],
            [
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ],

        );
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function test_MultipleFileUpload(): void
    {

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('upload');
        $crawler = $this->client->request(
            'POST',
            $this->UrlGenerator(self::UPLOAD_ROUTE),
            [
                '_token' => $csrfToken
            ],
            [
                'files' => $this->fileGenerator->createFiles(2)
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
