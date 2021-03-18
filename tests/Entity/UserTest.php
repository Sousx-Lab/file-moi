<?php
namespace App\Tests\Entity;


use App\Entity\Auth\User;
use App\Repository\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
;

final class UserTest extends KernelTestCase
{
    use FixturesTrait;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $purger = new ORMPurger($this->em);
        $purger->purge();    
    }

    private function getEntity(): User
    {
        return (new User())
        ->setEmail('john@doe.fr')
        ->setFirstname('John')
        ->setPassword('UserTestPassword1')
        ->setConfirmPassword('UserTestPassword1');
    }
    
    public function test_ValidUserEntity() :void
    {        
        $user = $this->getEntity();
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');

        $this->assertInstanceOf(User::class, $user);
        
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(0, $error);
    }

    public function test_UserUniqueUsername(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__, 1). '/Controller/users.yaml']);

        $user = $this->getEntity();
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');

        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function test_UserWithBadPasswordFormat(): void
    {
        $user = $this->getEntity();
        //Minimum length test
        $user->setPassword('Pass2');
        $user->setConfirmPassword('Pass2');
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(1, $error);
        
        //Uppercase test
        $user->setPassword('password1');
        $user->setConfirmPassword('password1');
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(1, $error);

        //Digit test
        $user->setPassword('Password');
        $user->setConfirmPassword('Password');
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function test_CreatedAtOnPrePersist(): void
    {
        $user = $this->getEntity();
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');
        self::$container->get('doctrine.orm.default_entity_manager')->persist($user);
        
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getCreateAt());

    }

    public function test_UpdateAtOnPreUpdate(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__, 1). '/Controller/users.yaml']);

        /**@var User|null */
        $user = self::$container->get(UserRepository::class)->findOneBy(['email' => 'john@doe.fr']);
        $user->setFirstname('JohnTest');
        self::$container->get('doctrine.orm.default_entity_manager')->flush();

        $user = self::$container->get(UserRepository::class)->findOneBy(['email' => 'john@doe.fr']);
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getUpdatedAt());
    }
}