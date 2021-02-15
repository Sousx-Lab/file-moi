<?php
namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
;

final class UserTest extends KernelTestCase
{
    use FixturesTrait;
    public function test_ValidUserEntity() :void
    {
        self::bootKernel();
        
        $user = (new User())
            ->setEmail('john@doe.fr')
            ->setFirstname('John');
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');

        $this->assertInstanceOf(User::class, $user);
        
        $error = self::$container->get('validator')->validate($user);

        $this->assertCount(0, $error);
    }

    public function test_UserUniqueUsername(): void
    {
        self::bootKernel();
        $this->loadFixtureFiles([dirname(__DIR__, 1). '/Controller/users.yaml']);

        $user = (new User())
            ->setEmail('john@doe.fr')
            ->setFirstname('John');
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');

        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function test_UserWithBadPasswordFormat(): void
    {
        self::bootKernel();
        $user = (new User())
            ->setEmail('john@doe.fr')
            ->setFirstname('John');
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
        self::bootKernel();

        $user = (new User())
            ->setEmail('john@doe.fr')
            ->setFirstname('John');
        $user->setPassword('UserTestPassword1');
        $user->setConfirmPassword('UserTestPassword1');
        self::$container->get('doctrine.orm.default_entity_manager')->persist($user);
        
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getCreateAt());

    }

    public function test_UpdateAtOnPreUpdate(): void
    {
        self::bootKernel();
        $this->loadFixtureFiles([dirname(__DIR__, 1). '/Controller/users.yaml']);

        /**@var User|null */
        $user = self::$container->get(UserRepository::class)->findOneBy(['email' => 'john@doe.fr']);
        $user->setFirstname('JohnTest');
        self::$container->get('doctrine.orm.default_entity_manager')->flush();

        $user = self::$container->get(UserRepository::class)->findOneBy(['email' => 'john@doe.fr']);
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getUpdatedAt());
    }
}