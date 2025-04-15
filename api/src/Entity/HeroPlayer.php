<?php

namespace App\Entity;

use App\Entity\UnitPlayer;
use App\Repository\HeroPlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HeroPlayerRepository::class)]
class HeroPlayer extends UnitPlayer
{
    #[ORM\OneToMany(mappedBy: 'heroPlayer', targetEntity: HeroPlayerAbility::class, orphanRemoval: true)]
    private Collection $abilities;

    #[ORM\Column]
    private ?int $gear_level = null;

    #[ORM\Column]
    private ?int $relic_level = null;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
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

    #[Groups('api_player_unit')]
    public function getGearLevel(): ?int
    {
        return $this->gear_level;
    }

    public function setGearLevel(int $gear_level): self
    {
        $this->gear_level = $gear_level;

        return $this;
    }

    #[Groups('api_player_unit')]
    public function getRelicLevel(): ?int
    {
        return $this->relic_level;
    }

    public function setRelicLevel(int $relic_level): self
    {
        $this->relic_level = $relic_level;

        return $this;
    }
}
