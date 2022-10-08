<?php

namespace App\Entity;

use App\Repository\UnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\InformationTrait;

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

    public function __construct()
    {
        $this->squadunits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseId(): ?string
    {
        return $this->base_id;
    }

    public function setBaseId(string $base_id): self
    {
        $this->base_id = $base_id;

        return $this;
    }

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
}
