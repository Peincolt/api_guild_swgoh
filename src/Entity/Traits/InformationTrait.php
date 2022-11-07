<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait InformationTrait
{
    #[ORM\Column(length:255)]
    private ?string $name = null;

    #[ORM\Column(length:255)]
    private ?string $id_swgoh = null;

    #[Groups(['api_unit','api_player','api_player_unit','api_guild', 'api_squad', 'api_squad_unit'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Groups(['api_guild','api_player', 'api_squad', 'api_squad_unit'])]
    public function getIdSwgoh(): ?string
    {
        return $this->id_swgoh;
    }

    public function setIdSwgoh(string $id_swgoh): self
    {
        $this->id_swgoh = $id_swgoh;

        return $this;
    }
}