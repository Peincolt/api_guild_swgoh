<?php

namespace App\Entity;

use App\Repository\HeroPlayerAbilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroPlayerAbilityRepository::class)]
class HeroPlayerAbility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isZetaLearned = null;

    #[ORM\Column]
    private ?bool $isOmicronLearned = null;

    #[ORM\ManyToOne(inversedBy: 'heroPlayerAbilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ability $ability = null;

    #[ORM\ManyToOne(inversedBy: 'heroPlayerAbilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HeroPlayer $heroPlayer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsZetaLearned(): ?bool
    {
        return $this->isZetaLearned;
    }

    public function setIsZetaLearned(bool $isZetaLearned): self
    {
        $this->isZetaLearned = $isZetaLearned;

        return $this;
    }

    public function isIsOmicronLearned(): ?bool
    {
        return $this->isOmicronLearned;
    }

    public function setIsOmicronLearned(bool $isOmicronLearned): self
    {
        $this->isOmicronLearned = $isOmicronLearned;

        return $this;
    }

    public function getAbility(): ?Ability
    {
        return $this->ability;
    }

    public function setAbility(?Ability $ability): self
    {
        $this->ability = $ability;

        return $this;
    }

    public function getHeroPlayer(): ?HeroPlayer
    {
        return $this->heroPlayer;
    }

    public function setHeroPlayer(?HeroPlayer $heroPlayer): self
    {
        $this->heroPlayer = $heroPlayer;

        return $this;
    }
}
