 <?php

use Faker\Provider\Uuid;
use App\Entity\File\File;
use App\Tests\Controller\File\FileGenerator;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DownloadControllerTest extends WebTestCase
{
    private const DOWNLOAD_ROUTE =  "route_file_download";

    use FixturesTrait;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function UrlGenerator(string $route, array $params = [], int $path = 1 ): string
    {
        /**@var UrlGeneratorInterface */
        $router = $this->client->getContainer()->get('router');
        return $router->generate($route, $params, $path);
    }

    public function test_DownloadFileNotFound(): void
    {
       $crawler = $this->client->request('GET', $this->UrlGenerator(self::DOWNLOAD_ROUTE, ['id' => Uuid::uuid()]));

       $this->assertRegExp('/File not found !/', $this->client->getResponse()->getContent());
       $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function test_DownloadFileExist(): void
    {
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        /**@var File */
        $file = $fixtures['file_file'];

        $crawler = $this->client->request(
                'GET',
                $this->UrlGenerator(self::DOWNLOAD_ROUTE, [
                    'id' => $file->getId()
                ])
            );
        $dlFileUrl = "dl/{$file->getId()}/{$file->getFileName()}" ;
        $link = $crawler->selectLink('Download');

        $this->assertEquals(1, $link->count());

        $this->assertEquals($link->link()->getUri(),  $this->UrlGenerator('route_homepage', [], 0). $dlFileUrl);

        $this->assertRegExp("/{$file->getFileName()}/", $this->client->getResponse()->getContent());
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

    }
}