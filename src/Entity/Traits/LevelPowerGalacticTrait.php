<?php

namespace App\Entity\Traits;

trait LevelPowerGalacticTrait
{
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column]
    private ?int $galactical_power = null;

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getGalacticalPower(): ?int
    {
        return $this->galactical_power;
    }

    public function setGalacticalPower(int $galactical_power): self
    {
        $this->galactical_power = $galactical_power;

        return $this;
    }
}