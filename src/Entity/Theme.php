<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ThemeRepository::class)
 */
class Theme
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="themes")
     * @ORM\JoinColumn(nullable=true)
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de donner un nom à votre thème.")
     * @Assert\Length(max=255, maxMessage="Le nom de votre thème ne peut pas faire plus de 255 caractères.")
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity=Meal::class, mappedBy="themes")
     */
    private Collection $meals;

    public function __construct(?string $uuid = null)
    {
        $this->id = null !== $uuid ? Uuid::fromString($uuid) : Uuid::v6();
        $this->meals = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Meal[]
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(Meal $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals[] = $meal;
            $meal->addTheme($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): self
    {
        if ($this->meals->removeElement($meal)) {
            $meal->removeTheme($this);
        }

        return $this;
    }
}
