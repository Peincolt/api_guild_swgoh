<?php

namespace App\Entity;

use App\Repository\ShipPlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipPlayerRepository::class)]
class ShipPlayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shipPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ship $ship = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): self
    {
        $this->ship = $ship;

        return $this;
    }
}
