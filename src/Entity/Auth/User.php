<?php

namespace App\Entity\Auth;

use App\Entity\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Auth\UserRepository;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="L'adresse ne peux pas étre utilisée")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L'adresse email ne peut pas être vide !")
     * @Assert\Email(message="L'email {{ email }} n'est pas un email valide")
     */
    private string $email;

    /**
     * @ORM\Column(name="firstname", type="string", length=180, nullable=true)
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe ne peux pas être vide !")
     * @Assert\Length(min=8, minMessage="Le mot de passe doit comporter au moins 8 caractères")
     * @Assert\Regex(pattern="/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])/", 
     *         message="Le mot de passe doit comporter au moins une lettre majuscule, une miniscule et un chiffre")
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\EqualTo(propertyPath="password", message="Veuillez confirmer le même mot de passe")
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmationToken = null;

    /**
     * @ORM\ManyToMany(targetEntity=File::class, mappedBy="user")
     */
    private $files;


    public function __construct()
    {
        $this->id = Uuid::uuid4()->__toString();
        $this->files = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): ?self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getCreateAt(): \DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $timestamp): self
    {
        $this->createAt = $timestamp;
        return $this;
    }

    /**
     * Set createdAt value on pre persistence
     * @ORM\PrePersist
     * @return void
     */
    public function onPrePersist(): void
    {
        if (null === $this->createAt) {
            $this->setCreateAt(new \DateTime('now'));
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Set updatedAt value on pre updated
     * @ORM\PreUpdate
     * @return void
     */
    public function onPreUpdate(): void
    {
        if (null === $this->updatedAt) {
            $this->setUpdatedAt(new \DateTime('now'));
        }
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

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->addUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            $file->removeUser($this);
        }

        return $this;
    }
}
