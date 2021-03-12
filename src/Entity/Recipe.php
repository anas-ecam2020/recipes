<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("recipe:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups("recipe:read")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=4, max=255, minMessage="Votre recette doit contenir au moins 4 caractères")
     * @Assert\NotBlank(message="Le titre est obligatoire")
     * @Groups("recipe:read")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10,  minMessage="Votre description doit contenir au moins 10 caractères")
     * @Assert\NotBlank(message="Le contenu est obligatoire")
     * @Groups("recipe:read")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("recipe:read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("recipe:read")
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("recipe:read")
     */
    private $favorite;

    /**
     * @ORM\Column(type="smallint")
     * @Groups("recipe:read")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("recipe:read")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="smallint")
     * @Groups("recipe:read")
     */
    private $portions;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="recipe", orphanRemoval=true)
     * @Groups("recipe:read")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(bool $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getPortions(): ?int
    {
        return $this->portions;
    }

    public function setPortions(int $portions): self
    {
        $this->portions = $portions;

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
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

        return $this;
    }
}
