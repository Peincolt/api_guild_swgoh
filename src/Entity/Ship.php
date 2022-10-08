<?php

namespace App\Entity;

use App\Repository\ShipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Unit;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
class Ship extends Unit
{

    #[ORM\OneToMany(mappedBy: 'ship', targetEntity: ShipPlayer::class, orphanRemoval: true)]
    private Collection $shipPlayers;

    public function __construct()
    {
        $this->shipPlayers = new ArrayCollection();
    }

    /**
     * @return Collection<int, ShipPlayer>
     */
    public function getShipPlayers(): Collection
    {
        return $this->shipPlayers;
    }

    public function addShipPlayer(ShipPlayer $shipPlayer): self
    {
        if (!$this->shipPlayers->contains($shipPlayer)) {
            $this->shipPlayers->add($shipPlayer);
            $shipPlayer->setShip($this);
        }

        return $this;
    }

    public function removeShipPlayer(ShipPlayer $shipPlayer): self
    {
        if ($this->shipPlayers->removeElement($shipPlayer)) {
            // set the owning side to null (unless already changed)
            if ($shipPlayer->getShip() === $this) {
                $shipPlayer->setShip(null);
            }
        }

        return $this;
    }
}
