<?php

namespace App\Entity;

use App\Repository\UnitPlayerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\LevelPowerGalacticTrait;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[ORM\MappedSuperclass]
class UnitPlayer
{
    use LevelPowerGalacticTrait;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $number_stars = null;

    #[ORM\Column]
    private ?int $protection = null;

    #[ORM\Column]
    private ?int $life = null;

    #[ORM\Column]
    private ?int $speed = null;

    #[ORM\ManyToOne(inversedBy: 'unitPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberStars(): ?int
    {
        return $this->number_stars;
    }

    public function setNumberStars(int $number_stars): self
    {
        $this->number_stars = $number_stars;

        return $this;
    }

    public function getProtection(): ?int
    {
        return $this->protection;
    }

    public function setProtection(int $protection): self
    {
        $this->protection = $protection;

        return $this;
    }

    public function getLife(): ?int
    {
        return $this->life;
    }

    public function setLife(int $life): self
    {
        $this->life = $life;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }
}
