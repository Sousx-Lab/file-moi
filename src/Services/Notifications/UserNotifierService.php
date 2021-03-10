<?php
namespace App\Services\Notifications;

use App\Entity\Auth\User;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserNotifierService {

    private MailerInterface $mailer;

    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function notify(User $user, array $emailData)
    {
        $email = (new Email())
            ->from('noreply@site.fr')
            ->subject($emailData['subject'])
            ->to($user->getEmail())
            ->html($this->twig->render('email/notification.html.twig', [
                'user' => $user,
                'subject' => $emailData['subject'],
                'message' => $emailData['message'], 
                'link' => $emailData['link'] ?? null
            ]));
        $this->mailer->send($email);
    }
}