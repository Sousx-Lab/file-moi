<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Messenger\Message\UserNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function registration(Request $request, 
        UserPasswordEncoderInterface $encoder, 
        EntityManagerInterface $em,
        MessageBusInterface $messageBus): Response
    {

        if (null !== $user = $this->getUser()) {
            $this->addFlash('info', 'You are already logged in as much as : ' . $user->getFirstname() ?? $user->getUsername());
            return $this->redirectToRoute('route_homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $this->isCsrfTokenValid('register-user', $request->get('token')) ) {
            
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            
            $messageBus->dispatch(new UserNotificationMessage($user->getId(), [
                'subject' => 'Account created',
                'message' =>  'Your account has been created successfully'
            ]));
    
            $this->addFlash('success', 'Your account has been created successfully');
            return $this->redirectToRoute('route_login');
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
