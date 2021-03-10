<?php
namespace App\Entity\Auth\Password\Data;


use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetConfirmData 
{
    /**
     * @Assert\NotBlank(message="Le mot de passe ne peux pas être vide !")
     * @Assert\Length(min=8, minMessage="Le mot de passe doit comporter au moins 8 caractères")
     * @Assert\Regex(pattern="/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])/", 
     *         message="Le mot de passe doit comporter au moins une lettre majuscule, une miniscule et un chiffre")
     */
    private string $password = '';

    /**
     * @Assert\NotBlank(message="Le mot de passe ne peux pas être vide !")
     * @Assert\EqualTo(propertyPath="password", message="Veuillez confirmer le même mot de passe")
     */
    private string $confirmPassword;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }
}