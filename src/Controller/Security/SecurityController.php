<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
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
     * @param Request $request
     * @Route("/registration", name="route_registration")
     * @return Response
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em): Response
    {

        if (null !== $user = $this->getUser()) {
            $this->addFlash('info', 'You are already logged in as much as : ' . $user->getFirstname() ?? $user->getUsername());
            if($referer = $request->headers->get('referer')){
                return new RedirectResponse($referer);
            }
            return $this->redirectToRoute('route_homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your account has been created successfully');
        }
        return $this->render("security/registration.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="route_logout")
     */
    public function logout()
    {
    }
}
