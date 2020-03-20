<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 1,
     *      max = 25,
     *      minMessage = "Le titre de l'article ne peut pas être vide",
     *      maxMessage = "Le titre de l'article ne peut pas faire plus de {{limit}} caractères"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 1,
     *      minMessage = "Le contenu de l'article ne peut pas être vide"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="text")
     * @Assert\File(
     * mimeTypes={"image/png", "image/jpeg"},
     * mimeTypesMessage = "Le format de votre fichier est invalide ({{ type }}). Les formats autorisés sont {{ types }}",
     * disallowEmptyMessage = "L\'article doit avoir une photo de couverture",
     * maxSizeMessage = "Votre fichier est trop lourd ({{size}} {{suffix}}. Le poids maximum est de {{limit}} {{suffix}}",
     * )
     * @Assert\NotBlank(
     * message = "L'article doit avoir une photo de couverture2",
     * groups = {"creation"}
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        //On rentre la date de création automatiquement
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
    * @ORM\PostRemove
    */
    public function deleteFile() 
    {
        if(file_exists(__DIR__ . '/../../public/assets/uploads/articles/'.$this->image)) {
            unlink(__DIR__ . '/../../public/assets/uploads/articles/'.$this->image);
        }
        return true;
    }

    public function deleteFileOnUpdate(String $previousImage) 
    {
        if(file_exists(__DIR__ . '/../../public/assets/uploads/articles/'.$previousImage)) {
            unlink(__DIR__ . '/../../public/assets/uploads/articles/'.$previousImage);
        }
        return true;
    }
}
