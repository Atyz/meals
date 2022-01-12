<?php

namespace App\Entity;

use App\Repository\ShoppingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShoppingRepository::class)
 */
class Shopping
{
    public const STATUS_TO_TAKE = false;
    public const STATUS_TAKEN = true;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="shoppings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $menu;

    /**
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="shoppings")
     * @ORM\JoinColumn(nullable=true)
     */
    private $ingredient;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status = self::STATUS_TO_TAKE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"free"})
     * @Assert\Length(max=255, groups={"free"})
     */
    private $freename;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $freecategory = null;

    public function __construct(?string $uuid = null)
    {
        $this->id = null !== $uuid ? Uuid::fromString($uuid) : Uuid::v6();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFreename(): ?string
    {
        return $this->freename;
    }

    public function setFreename(?string $freename): self
    {
        $this->freename = $freename;

        return $this;
    }

    public function getFreecategory(): ?Category
    {
        return $this->freecategory;
    }

    public function setFreecategory(?Category $freecategory): self
    {
        $this->freecategory = $freecategory;

        return $this;
    }

    public function isToTake(): bool
    {
        return self::STATUS_TO_TAKE === $this->getStatus();
    }

    public function isFree(): bool
    {
        return null !== $this->freename;
    }

    public function getName(): string
    {
        if (null === $this->getIngredient()) {
            return $this->getFreename();
        }

        return $this->getIngredient()->getName();
    }

    public function getCategory(): ?Category
    {
        if (null === $this->getIngredient()) {
            return $this->getFreecategory();
        }

        return $this->getIngredient()->getCategory();
    }
}
