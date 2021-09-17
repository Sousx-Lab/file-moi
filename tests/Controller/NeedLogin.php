<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

trait NeedLogin
{

    public function login(KernelBrowser $client, UserInterface $user): void
    {   
        $client->getKernel()->boot();
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $coockie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($coockie);
    }
}
