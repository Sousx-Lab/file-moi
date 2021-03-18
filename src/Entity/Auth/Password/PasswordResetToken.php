<?php
namespace App\Entity\Auth\Password;



use App\Entity\Auth\User;
use App\Repository\Auth\PasswordResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PasswordResetTokenRepository::class)
 */
class PasswordResetToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Auth\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of user
     */ 
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     * @returnself
     */ 
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}
