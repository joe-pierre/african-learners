<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 * @ORM\Table(name="courses")
 * @UniqueEntity(
 *     fields={"createdAt", "slug"},
 *     errorPath="slug",
 *     message="Ce cours existe déjà veuillez le renomer pour question de validité.Merci!"
 * )
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Course
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre est obligatoire")
     * @Assert\Length(min=3, minMessage="Le titre doit faire au moins {{ limit }} caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner une description")
     * @Assert\Length(min=10, minMessage="La description doit faire au moins {{ limit }} caractères")
     */
    private $description;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="course_image", fileNameProperty="imageName")
     * @Assert\Image(maxSize="2M", maxSizeMessage="L'image ne doit pas dépasser {{limit}} ")
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="course")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="course_file", fileNameProperty="fileName")
     * 
     * @var File|null
     */
    private $courseFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $courseFile
     */
    public function setCourseFile(?File $courseFile = null): void
    {
        $this->courseFile = $courseFile;

        if (null !== $courseFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTimeImmutable);
        }
    }

    public function getCourseFile(): ?File
    {
        return $this->courseFile;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPathParams(): array
    {
        return [
            'year' => $this->getCreatedAt()->format('Y'),
            'month' => $this->getCreatedAt()->format('m'),
            'day' => $this->getCreatedAt()->format('d'),
            'slug' => $this->getSlug()
        ];
    }
}