<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=Week::class, inversedBy="menus")
     */
    private $week;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="menus")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=MenuDay::class, mappedBy="menu", orphanRemoval=true, cascade={"persist"})
     */
    private $days;

    /**
     * @ORM\OneToMany(targetEntity=Shopping::class, mappedBy="menu", orphanRemoval=true)
     */
    private $shoppings;

    public function __construct(?string $uuid = null)
    {
        $this->id = null !== $uuid ? Uuid::fromString($uuid) : Uuid::v6();
        $this->days = new ArrayCollection();
        $this->shoppings = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getWeek(): ?Week
    {
        return $this->week;
    }

    public function setWeek(?Week $week): self
    {
        $this->week = $week;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(MenuDay $day): self
    {
        if (!$this->days->contains($day)) {
            $this->days[] = $day;
            $day->setMenu($this);
        }

        return $this;
    }

    public function removeDay(MenuDay $day): self
    {
        if ($this->days->removeElement($day)) {
            if ($day->getMenu() === $this) {
                $day->setMenu(null);
            }
        }

        return $this;
    }

    public function getShoppings(): Collection
    {
        return $this->shoppings;
    }

    public function addShopping(Shopping $shopping): self
    {
        if (!$this->shoppings->contains($shopping)) {
            $this->shoppings[] = $shopping;
        }

        return $this;
    }

    public function removeShopping(Shopping $shopping): self
    {
        if ($this->shoppings->removeElement($shopping)) {
            if ($shopping->getMenu() === $this) {
                $shopping->setMenu(null);
            }
        }

        return $this;
    }
}
