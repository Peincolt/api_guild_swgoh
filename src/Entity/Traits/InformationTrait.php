<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait InformationTrait
{
    #[ORM\Column(length:255)]
    private ?string $name = null;

    #[ORM\Column(length:255)]
    private ?string $id_swgoh = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

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