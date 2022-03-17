<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

 /**
  * @ORM\Entity(repositoryClass=UserRepository::class)
  * @ORM\Table(name="`user`")
  * @UniqueEntity("email", message="Il existe déjà un compte avec cet email.")
  */
 class User implements UserInterface, PasswordAuthenticatedUserInterface
 {
     /**
      * @ORM\Id
      * @ORM\Column(type="uuid", unique=true)
      */
     private Uuid $id;

     /**
      * @ORM\Column(type="string", length=180, unique=true)
      */
     private string $email;

     /**
      * @ORM\Column(type="json")
      */
     private array $roles = [];

     /**
      * @ORM\Column(type="string")
      */
     private ?string $password;

     /**
      * @Assert\NotBlank()
      */
     private ?string $plainPassword;

     /**
      * @ORM\OneToMany(targetEntity=UserPassword::class, mappedBy="user", orphanRemoval=true)
      */
     private Collection $userPasswords;

     /**
      * @ORM\OneToMany(targetEntity=Meal::class, mappedBy="user", orphanRemoval=true)
      */
     private Collection $meals;

     /**
      * @ORM\OneToMany(targetEntity=Theme::class, mappedBy="user", orphanRemoval=true)
      */
     private $themes;

     /**
      * @ORM\OneToMany(targetEntity=Week::class, mappedBy="user", orphanRemoval=true)
      */
     private $weeks;

     /**
      * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="user")
      */
     private $menus;

     /**
      * @ORM\Column(type="string", length=10, nullable=true)
      */
     private $refererCode;

     /**
      * @ORM\ManyToOne(targetEntity=User::class, inversedBy="referreds")
      */
     private $referer;

     /**
      * @ORM\OneToMany(targetEntity=User::class, mappedBy="referer")
      */
     private $referreds;

     public function __construct()
     {
         $this->id = Uuid::v6();
         $this->userPasswords = new ArrayCollection();
         $this->meals = new ArrayCollection();
         $this->themes = new ArrayCollection();
         $this->weeks = new ArrayCollection();
         $this->menus = new ArrayCollection();
         $this->referreds = new ArrayCollection();
     }

     public function getId(): Uuid
     {
         return $this->id;
     }

     public function getEmail(): ?string
     {
         return $this->email;
     }

     public function setEmail(string $email): self
     {
         $this->email = $email;

         return $this;
     }

     public function getUserIdentifier(): string
     {
         return (string) $this->email;
     }

     public function getRoles(): array
     {
         $roles = $this->roles;
         // guarantee every user at least has ROLE_USER
         $roles[] = 'ROLE_USER';

         return array_unique($roles);
     }

     public function setRoles(array $roles): self
     {
         $this->roles = $roles;

         return $this;
     }

     public function getPassword(): string
     {
         return $this->password;
     }

     public function setPassword(string $password): self
     {
         $this->password = $password;

         return $this;
     }

     public function getPlainPassword(): string
     {
         return $this->plainPassword;
     }

     public function setPlainPassword(string $plainPassword): self
     {
         $this->plainPassword = $plainPassword;
         $this->password = null;

         return $this;
     }

     public function eraseCredentials()
     {
         $this->plainPassword = null;
     }

     public function getUserPasswords(): Collection
     {
         return $this->userPasswords;
     }

     public function addUserPassword(UserPassword $userPassword): self
     {
         if (!$this->userPasswords->contains($userPassword)) {
             $this->userPasswords[] = $userPassword;
             $userPassword->setUser($this);
         }

         return $this;
     }

     public function removeUserPassword(UserPassword $userPassword): self
     {
         if ($this->userPasswords->removeElement($userPassword)) {
             // set the owning side to null (unless already changed)
             if ($userPassword->getUser() === $this) {
                 $userPassword->setUser(null);
             }
         }

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
             $meal->setUser($this);
         }

         return $this;
     }

     public function removeMeal(Meal $meal): self
     {
         if ($this->meals->removeElement($meal)) {
             // set the owning side to null (unless already changed)
             if ($meal->getUser() === $this) {
                 $meal->setUser(null);
             }
         }

         return $this;
     }

     public function getThemes(): Collection
     {
         return $this->themes;
     }

     public function addTheme(Theme $theme): self
     {
         if (!$this->themes->contains($theme)) {
             $this->themes[] = $theme;
             $theme->setUser($this);
         }

         return $this;
     }

     public function removeTheme(Theme $theme): self
     {
         if ($this->themes->removeElement($theme)) {
             // set the owning side to null (unless already changed)
             if ($theme->getUser() === $this) {
                 $theme->setUser(null);
             }
         }

         return $this;
     }

     public function getWeeks(): Collection
     {
         return $this->weeks;
     }

     public function addWeek(Week $week): self
     {
         if (!$this->weeks->contains($week)) {
             $this->weeks[] = $week;
             $week->setUser($this);
         }

         return $this;
     }

     public function removeWeek(Week $week): self
     {
         if ($this->weeks->removeElement($week)) {
             // set the owning side to null (unless already changed)
             if ($week->getUser() === $this) {
                 $week->setUser(null);
             }
         }

         return $this;
     }

     public function getMenus(): Collection
     {
         return $this->menus;
     }

     public function addMenu(Menu $menu): self
     {
         if (!$this->menus->contains($menu)) {
             $this->menus[] = $menu;
             $menu->setUser($this);
         }

         return $this;
     }

     public function removeMenu(Menu $menu): self
     {
         if ($this->menus->removeElement($menu)) {
             // set the owning side to null (unless already changed)
             if ($menu->getUser() === $this) {
                 $menu->setUser(null);
             }
         }

         return $this;
     }

     public function getRefererCode(): ?string
     {
         return $this->refererCode;
     }

     public function setRefererCode(?string $refererCode): self
     {
         $this->refererCode = $refererCode;

         return $this;
     }

     public function getReferer(): ?self
     {
         return $this->referer;
     }

     public function setReferer(?self $referer): self
     {
         $this->referer = $referer;

         return $this;
     }

     public function getReferreds(): Collection
     {
         return $this->referreds;
     }

     public function addReferred(self $referred): self
     {
         if (!$this->referreds->contains($referred)) {
             $this->referreds[] = $referred;
             $referred->setReferer($this);
         }

         return $this;
     }

     public function removeReferred(self $referred): self
     {
         if ($this->referreds->removeElement($referred)) {
             // set the owning side to null (unless already changed)
             if ($referred->getReferer() === $this) {
                 $referred->setReferer(null);
             }
         }

         return $this;
     }
 }
