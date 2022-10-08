<?php

namespace App\Entity;

use App\Repository\HeroPlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UnitPlayer;

#[ORM\Entity(repositoryClass: HeroPlayerRepository::class)]
class HeroPlayer extends UnitPlayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'heroPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hero $hero = null;

    #[ORM\OneToMany(mappedBy: 'heroPlayer', targetEntity: HeroPlayerAbility::class, orphanRemoval: true)]
    private Collection $abilities;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHero(): ?Hero
    {
        return $this->hero;
    }

    public function setHero(?Hero $hero): self
    {
        $this->hero = $hero;

        return $this;
    }

    /**
     * @return Collection<int, HeroPlayerAbility>
     */
    public function getHeroPlayerAbilities(): Collection
    {
        return $this->heroPlayerAbilities;
    }

    public function addHeroPlayerAbility(HeroPlayerAbility $heroPlayerAbility): self
    {
        if (!$this->abilities->contains($heroPlayerAbility)) {
            $this->abilities->add($heroPlayerAbility);
            $heroPlayerAbility->setHeroPlayer($this);
        }

        return $this;
    }

    public function removeHeroPlayerAbility(HeroPlayerAbility $heroPlayerAbility): self
    {
        if ($this->abilities->removeElement($heroPlayerAbility)) {
            // set the owning side to null (unless already changed)
            if ($heroPlayerAbility->getHeroPlayer() === $this) {
                $heroPlayerAbility->setHeroPlayer(null);
            }
        }

        return $this;
    }
}
