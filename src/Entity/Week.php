<?php

namespace App\Entity;

use App\Repository\WeekRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=WeekRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Week
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de donner un nom à votre semaine type.")
     * @Assert\Length(max=255, maxMessage="Le nom de votre semaine type ne peut pas faire plus de 255 caractères.")
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="weeks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\OneToMany(targetEntity=WeekDay::class, mappedBy="week", orphanRemoval=true, cascade={"persist"})/**
     */
    private Collection $days;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->days = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function hasDay(WeekDay $checkday): bool
    {
        foreach ($this->getDays() as $day) {
            if (
                $day->getDay() === $checkday->getDay() &&
                $day->getTime() === $checkday->getTime()
            ) {
                return true;
            }
        }

        return false;
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(WeekDay $day): void
    {
        if (!$this->hasDay($day)) {
            $this->days[$day->getDay().$day->getTime()] = $day;
            $day->setWeek($this);
        }
    }

    public function removeDay(WeekDay $day): void
    {
        $this->days->removeElement($day);
        $day->setWeek(null);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $totalDays = 0;

        foreach ($this->getDays() as $day) {
            if (!$day->isEmpty()) {
                ++$totalDays;
            }
        }

        if (0 === $totalDays) {
            $context->buildViolation('Vous devez sélectionner au moins un repas.')
                ->atPath('days')
                ->addViolation()
            ;
        }
    }
}
