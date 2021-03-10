<?php

namespace App\Controller\Security;

use App\Entity\Auth\User;
use App\Form\Security\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Messenger\Message\UserNotificationMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController extends AbstractController
{

    /**
     * @param Request $request
     * @Route("/registration", name="route_registration")
     * @return Response
     */
    public function registration(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        MessageBusInterface $messageBus
    ): Response {

        if (null !== $user = $this->getUser()) {
            $this->addFlash('info', 'You are already logged in as much as : ' . $user->getFirstname() ?? $user->getUsername());
            return $this->redirectToRoute('route_homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $this->isCsrfTokenValid('register-user', $request->get('token'))) {

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            $messageBus->dispatch(new UserNotificationMessage(
                $user->getId(),
                [
                    'subject' => 'Account created',
                    'message' =>  'Your account has been created successfully'
                ]
            ));

            $this->addFlash('success', 'Your account has been created successfully');
            return $this->redirectToRoute('route_login');
        }
        return $this->render("security/registration.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
