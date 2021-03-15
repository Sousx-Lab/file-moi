<?php

use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\EventsSubscriber\Mailing\AuthSubscriber;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Events\Auth\Password\PasswordResetTokenCreatedEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthSubscriberTest extends KernelTestCase
{
    use FixturesTrait;

    private EntityManagerInterface $em;

    private $token;
    private $user;

    protected function setUp(): void
    {
        BypassFinals::enable();
        self::bootKernel();
        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $purger = new ORMPurger($this->em);
        $purger->purge();

        $fixtures = $this->loadFixtureFiles([dirname(__DIR__, 1) . "/Controller/users.yaml"]);
        $this->user = $fixtures['user_user'];
        $this->token = $fixtures['token_token'];
    }

    public function dispatch($messageBus)
    {

        /**@var UrlGeneratorInterface */
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subscriber = new AuthSubscriber($messageBus, $urlGenerator);
        $event = new PasswordResetTokenCreatedEvent($this->token);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }

    public function test_EventSubscription(): void
    {
        $this->assertArrayHasKey(PasswordResetTokenCreatedEvent::class, AuthSubscriber::getSubscribedEvents());
    }

    public function test_OnPasswordResetEventDispatch(): void
    {
        $messageBus = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $messageBus->expects($this->once())->method('dispatch');
        $this->dispatch($messageBus);
    }
}
