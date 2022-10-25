<?php

namespace App\Entity;

use App\Entity\UnitPlayer;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UnitRepository;
use App\Entity\Traits\InformationTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: UnitRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(array('ship' => 'Ship', 'hero'=>'Hero'))]
class Unit
{
    use InformationTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $base_id = null;

    #[ORM\Column]
    private array $categories = [];

    #[ORM\OneToMany(mappedBy: 'unit', targetEntity: SquadUnit::class, orphanRemoval: true)]
    private Collection $squadunits;

    #[ORM\OneToMany(mappedBy: 'unit', targetEntity: UnitPlayer::class, orphanRemoval: true)]
    private Collection $playerUnits;

    public function __construct()
    {
        $this->squadunits = new ArrayCollection();
        $this->playerUnits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['api_unit'])]
    public function getBaseId(): ?string
    {
        return $this->base_id;
    }

    public function setBaseId(string $base_id): self
    {
        $this->base_id = $base_id;

        return $this;
    }

    #[Groups(['api_unit'])]
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, SquadUnit>
     */
    public function getSquadunits(): Collection
    {
        return $this->squadunits;
    }

    public function addSquadunit(SquadUnit $squadunit): self
    {
        if (!$this->squadunits->contains($squadunit)) {
            $this->squadunits->add($squadunit);
            $squadunit->setUnit($this);
        }

        return $this;
    }

    public function removeSquadunit(SquadUnit $squadunit): self
    {
        if ($this->squadunits->removeElement($squadunit)) {
            // set the owning side to null (unless already changed)
            if ($squadunit->getUnit() === $this) {
                $squadunit->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerUnit>
     */
    public function getPlayerUnits(): Collection
    {
        return $this->playerUnits;
    }

    public function addPlayerUnit(UnitPlayer $playerUnit): self
    {
        if (!$this->playerUnits->contains($playerUnit)) {
            $this->playerUnits->add($playerUnit);
            $playerUnit->setUnit($this);
        }

        return $this;
    }

    public function removePlayerUnit(UnitPlayer $playerUnit): self
    {
        if ($this->playerUnits->removeElement($playerUnit)) {
            // set the owning side to null (unless already changed)
            if ($playerUnit->getUnit() === $this) {
                $playerUnit->setUnit(null);
            }
        }

        return $this;
    }
}
