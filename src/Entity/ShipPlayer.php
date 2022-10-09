<?php

namespace App\Entity;

use App\Entity\UnitPlayer;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShipPlayerRepository;

#[ORM\Entity(repositoryClass: ShipPlayerRepository::class)]
class ShipPlayer extends UnitPlayer
{

}
