<?php

namespace App\Entity;

use App\Repository\SquadUnitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SquadUnitRepository::class)]
class SquadUnit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $show_order = null;

    #[ORM\ManyToOne(inversedBy: 'units')]
    #[ORM\JoinColumn(nullable: false)]
    private ?squad $squad = null;

    #[ORM\ManyToOne(inversedBy: 'squadunits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $unit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowOrder(): ?int
    {
        return $this->show_order;
    }

    public function setShowOrder(int $show_order): self
    {
        $this->show_order = $show_order;

        return $this;
    }

    public function getSquad(): ?squad
    {
        return $this->squad;
    }

    public function setSquad(?squad $squad): self
    {
        $this->squad = $squad;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
