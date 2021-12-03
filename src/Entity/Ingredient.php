<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    public const SEASONALITY_WINTER = 1;
    public const SEASONALITY_SPRING = 2;
    public const SEASONALITY_SUMMER = 3;
    public const SEASONALITY_AUTUMN = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de donner un nom à votre ingrédient.")
     * @Assert\Length(max=255, maxMessage="Le nom de votre ingrédient ne peut pas faire plus de 255 caractères.")
     */
    private string $name;

    /**
     * @ORM\Column(type="array")
     */
    private $seasonality = [];

    /**
     * @ORM\ManyToMany(targetEntity=Meal::class, mappedBy="ingredients")
     */
    private Collection $meals;

    /**
     * @ORM\OneToMany(targetEntity=Shopping::class, mappedBy="ingredient")
     */
    private Collection $shoppings;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="ingredients")
     */
    private $category;

    public function __construct(?string $uuid = null)
    {
        $this->id = null !== $uuid ? Uuid::fromString($uuid) : Uuid::v6();
        $this->meals = new ArrayCollection();
        $this->shoppings = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public static function getSeasonalities(): array
    {
        return array_flip([
            self::SEASONALITY_WINTER => 'Hiver',
            self::SEASONALITY_SPRING => 'Printemps',
            self::SEASONALITY_SUMMER => 'Eté',
            self::SEASONALITY_AUTUMN => 'Automne',
        ]);
    }

    public function getSeasonality(): ?array
    {
        return $this->seasonality;
    }

    public function setSeasonality(array $seasonality): self
    {
        $this->seasonality = $seasonality;

        return $this;
    }

    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(Meal $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals[] = $meal;
            $meal->addIngredient($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): self
    {
        if ($this->meals->removeElement($meal)) {
            $meal->removeIngredient($this);
        }

        return $this;
    }

    public function getShoppings(): Collection
    {
        return $this->shoppings;
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
}
