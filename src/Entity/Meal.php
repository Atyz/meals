<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MealRepository::class)
 */
class Meal
{
    const PREP_EXPRESS = 1;
    const PREP_FAST = 2;
    const PREP_BASIC = 3;
    const PREP_LONG = 4;

    const RECURRENCE_NONE = 0;
    const RECURRENCE_EVERY_WEEK = 1;
    const RECURRENCE_TWO_WEEK = 2;
    const RECURRENCE_THREE_WEEK = 3;
    const RECURRENCE_FOUR_WEEK = 4;
    const RECURRENCE_FIVE_WEEK = 5;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="meals")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de donner un nom à votre plat.")
     * @Assert\Length(max=255, maxMessage="Le nom de votre plat ne peut pas faire plus de 255 caractères.")
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Merci d'indiquer un temps de préparation.")
     * @Assert\Choice(callback="getPreparations")
     */
    private int $preparation;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(callback="getRecurrences")
     */
    private int $recurrence = self::RECURRENCE_NONE;

    /**
     * @ORM\ManyToMany(targetEntity=Ingredient::class, inversedBy="meals")
     * @ORM\OrderBy({"name" = "ASC"})
     * @Assert\Count(min=1, minMessage="Merci de sélectionner au moins 1 ingrédient.")
     */
    private Collection $ingredients;

    /**
     * @ORM\ManyToMany(targetEntity=Theme::class, inversedBy="meals")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private Collection $themes;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->ingredients = new ArrayCollection();
        $this->themes = new ArrayCollection();
    }

    public static function getPreparations(): array
    {
        return array_flip([
            self::PREP_EXPRESS => 'Expresse',
            self::PREP_FAST => 'Rapide',
            self::PREP_BASIC => 'Normale',
            self::PREP_LONG => 'Long',
        ]);
    }

    public static function getRecurrences(): array
    {
        return array_flip([
            self::RECURRENCE_NONE => 'Aucune récurrence particulière',
            self::RECURRENCE_EVERY_WEEK => 'Chaque semaine',
            self::RECURRENCE_TWO_WEEK => 'Toutes les 2 semaines',
            self::RECURRENCE_THREE_WEEK => 'Toutes les 3 semaines',
            self::RECURRENCE_FOUR_WEEK => 'Toutes les 4 semaines',
            self::RECURRENCE_FIVE_WEEK => 'Toutes les 5 semaines',
        ]);
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

    public function getPreparation(): ?int
    {
        return $this->preparation;
    }

    public function getPreparationLabel(): string
    {
        return array_search($this->getPreparation(), self::getPreparations());
    }

    public function setPreparation(int $preparation): self
    {
        $this->preparation = $preparation;

        return $this;
    }

    public function getRecurrence(): ?int
    {
        return $this->recurrence;
    }

    public function getRecurrenceLabel(): string
    {
        return array_search($this->getRecurrence(), self::getRecurrences());
    }

    public function setRecurrence(int $recurrence): self
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->addMeal($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            $ingredient->removeMeal($this);
        }

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->addMeal($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            $theme->removeMeal($this);
        }

        return $this;
    }
}
