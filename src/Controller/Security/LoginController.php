<?php

namespace App\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="route_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $user = $this->getUser()) {
            $this->addFlash('info', 'You are already logged in as much as : ' . $user->getFirstname() ?? $user->getUsername());
            return $this->redirectToRoute('route_homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="route_logout")
     */
    public function logout()
    {
    }
}
