<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

trait LevelPowerGalacticTrait
{
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column]
    private ?int $galactical_power = null;

    #[Groups('api_player','api_player_unit')]
    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    #[Groups('api_player','api_player_unit')]
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