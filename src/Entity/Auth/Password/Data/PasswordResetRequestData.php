<?php
namespace App\Entity\Auth\Password\Data;


use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetRequestData
{
    /**
     * @Assert\NotBlank(message="L'adresse email ne peut pas être vide !")
     * @Assert\Email(message="L'email {{ email }} n'est pas un email valide")
     */
    private string $email = '';

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}