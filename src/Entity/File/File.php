<?php

namespace App\Entity\File;

use App\Entity\Auth\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use App\Repository\File\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File As Files;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class File
{
     /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private string $id;

    /**
     * @var Files|null
     * @Assert\File
     * @Vich\UploadableField(mapping="file_dl", fileNameProperty="fileName", size="fileSize", mimeType="mimeType")
     */
    private ?Files $uploadedFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $fileName = '';
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $fileSize = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $mimeType = '';

    /**
     * @ORM\Column(type="datetime")
    */
    private ?\DateTimeInterface $createAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="files")
     */
    private $user;

    public function __construct() {
        $this->id = Uuid::uuid4()->__toString();
        $this->user = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getCreateAt(): \DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $timestemp): self
    {
        $this->createAt = $timestemp;
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
 
    public function getUploadedFile(): ?Files
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?Files $file)
    {
        $this->uploadedFile = $file;

        if($file){
            $this->updatedAt = new \DateTime('now');
        }
    }
    
    /**
     * @return Collection|User[]
     */
    public function getUser(): ?Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize($fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get the value of mimeType
     */ 
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType($mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

}
