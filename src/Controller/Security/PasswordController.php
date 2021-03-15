<?php

namespace App\Controller\Security;

use App\Entity\Auth\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Auth\Password\PasswordResetToken;
use App\Services\Security\Password\PasswordService;
use App\Form\Security\Password\PasswordResetConfirmType;
use App\Form\Security\Password\PasswordResetRequestType;
use App\Entity\Auth\Password\Data\PasswordResetConfirmData;
use App\Entity\Auth\Password\Data\PasswordResetRequestData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class PasswordController extends AbstractController
{

    /**
     * @Route("/password/reset", name="route_password_reset")
     * @param Request $request
     * @return Response
     */
    public function reset(Request $request, PasswordService $resetService): Response
    {
        $error = null;
        $data = new PasswordResetRequestData();
        $form = $this->createForm(PasswordResetRequestType::class, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $resetService->resetPassword($form->getData());
                $this->addFlash('success', 'The instructions for resetting your password have been sent by email');

                return $this->redirectToRoute('route_login');
            } catch (AuthenticationException $e) {
                $error = $e;
            }
        }
        return $this->render('security/reset.password.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset/new/{id}/{token}", requirements={"id": "%routing.uuid%"}, name="route_update_password")
     * @Entity("token", expr="repository.findOneByToken(token)")
     */
    public function confirm(Request $request, User $user, ?PasswordResetToken $token, PasswordService $resetService): Response
    {
        if (!$token || $resetService->isExpired($token) || $token->getUser() !== $user) {
            $this->addFlash('error', 'This token is expired');
            return $this->redirectToRoute('route_login');
        }
        $error = null;
        $data = new PasswordResetConfirmData();
        $form = $this->createForm(PasswordResetConfirmType::class, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resetService->updatePassword($data->getPassword(), $token);
            $this->addFlash('success', 'Your password has been updated successfuly');

            return $this->redirectToRoute('route_login');
        }
        return $this->render('security/update.password.html.twig', [
            'error' => $error,
            'form'  => $form->createView(),
        ]);
    }
}
