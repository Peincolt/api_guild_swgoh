<?php

namespace App\Entity;

use App\Repository\HeroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Unit;

#[ORM\Entity(repositoryClass: HeroRepository::class)]
class Hero extends Unit
{
    #[ORM\OneToMany(mappedBy: 'hero', targetEntity: Ability::class, orphanRemoval: true)]
    private Collection $abilities;

    #[ORM\OneToMany(mappedBy: 'hero', targetEntity: HeroPlayer::class, orphanRemoval: true)]
    private Collection $heroPlayers;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
        $this->heroPlayers = new ArrayCollection();
    }

    /**
     * @return Collection<int, Ability>
     */
    public function getAbilities(): Collection
    {
        return $this->abilities;
    }

    public function addAbility(Ability $ability): self
    {
        if (!$this->abilities->contains($ability)) {
            $this->abilities->add($ability);
            $ability->setHero($this);
        }

        return $this;
    }

    public function removeAbility(Ability $ability): self
    {
        if ($this->abilities->removeElement($ability)) {
            // set the owning side to null (unless already changed)
            if ($ability->getHero() === $this) {
                $ability->setHero(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HeroPlayer>
     */
    public function getHeroPlayers(): Collection
    {
        return $this->heroPlayers;
    }

    public function addHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if (!$this->heroPlayers->contains($heroPlayer)) {
            $this->heroPlayers->add($heroPlayer);
            $heroPlayer->setHero($this);
        }

        return $this;
    }

    public function removeHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if ($this->heroPlayers->removeElement($heroPlayer)) {
            // set the owning side to null (unless already changed)
            if ($heroPlayer->getHero() === $this) {
                $heroPlayer->setHero(null);
            }
        }

        return $this;
    }
}
