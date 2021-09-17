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

    public function test_DownloadFileNotFound(): void
    {
       $crawler = self::$client->request('GET', $this->UrlGenerator(self::DOWNLOAD_ROUTE, ['id' => Uuid::uuid()]));

       $this->assertMatchesRegularExpression('/File not found !/', self::$client->getResponse()->getContent());
       $this->assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
    }

    public function test_DownloadFileExist(): void
    {
        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . '/users.yaml']);

        /**@var File */
        $file = $fixtures['file_file'];

        $crawler = self::$client->request(
                'GET',
                $this->UrlGenerator(self::DOWNLOAD_ROUTE, [
                    'id' => $file->getId()
                ])
            );
        $dlFileUrl = "dl/{$file->getId()}/{$file->getFileName()}" ;
        $link = $crawler->selectLink('Download');

        $this->assertEquals(1, $link->count());

        $this->assertEquals($link->link()->getUri(),  $this->UrlGenerator('route_homepage', [], 0). $dlFileUrl);

        $this->assertMatchesRegularExpression("/{$file->getFileName()}/", self::$client->getResponse()->getContent());
        
        $this->assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());

    }
}