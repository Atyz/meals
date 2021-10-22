<?php

namespace App\Entity;

use App\Repository\WeekDayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=WeekDayRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class WeekDay
{
    private const MONDAY = 1;
    private const TUESDAY = 2;
    private const WEDNESDAY = 3;
    private const THURSDAY = 4;
    private const FRIDAY = 5;
    private const SATURDAY = 6;
    private const SUNDAY = 7;

    private const TIME_LUNCH = 1;
    private const TIME_DINNER = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $day;

    /**
     * @ORM\Column(type="integer")
     */
    private int $time;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $used = false;

    /**
     * @ORM\ManyToOne(targetEntity=Week::class, inversedBy="days")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Week $week;

    /**
     * @ORM\ManyToMany(targetEntity=Theme::class)
     */
    private Collection $themes;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private array $preparations = [];

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->themes = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public static function getDays(): array
    {
        return [
            self::MONDAY => 'lundi',
            self::TUESDAY => 'mardi',
            self::WEDNESDAY => 'mercredi',
            self::THURSDAY => 'jeudi',
            self::FRIDAY => 'vendredi',
            self::SATURDAY => 'samedi',
            self::SUNDAY => 'dimanche',
        ];
    }

    public function getDayLabel()
    {
        $labels = self::getDays();

        return $labels[$this->getDay()] ?? '-';
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

    public static function getTimes(): array
    {
        return [
            self::TIME_LUNCH => 'déjeuner',
            self::TIME_DINNER => 'dînner',
        ];
    }

    public function getTimeLabel()
    {
        $labels = self::getTimes();

        return $labels[$this->getTime()] ?? '-';
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
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
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        $this->themes->removeElement($theme);

        return $this;
    }

    public function getPreparations(): ?array
    {
        return $this->preparations;
    }

    public function setPreparations(?array $preparations): self
    {
        $this->preparations = $preparations;

        return $this;
    }

    public function isEmpty(): bool
    {
        return
            false === $this->isUsed() &&
            0 === count($this->getThemes()) &&
            0 === count($this->getPreparations())
        ;
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function updateUsed()
    {
        if (true === $this->isUsed()) {
            return;
        }

        if (0 < count($this->getPreparations()) || 0 < count($this->getThemes())) {
            $this->used = true;
        }
    }
}
