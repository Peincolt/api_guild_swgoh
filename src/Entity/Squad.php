<?php

namespace App\Entity;

use App\Repository\SquadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SquadRepository::class)]
class Squad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $used_for = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToMany(targetEntity: Guild::class, inversedBy: 'squads')]
    private Collection $guilds;

    #[ORM\OneToMany(mappedBy: 'squad', targetEntity: Squadunit::class, orphanRemoval: true)]
    private Collection $units;

    public function __construct()
    {
        $this->guilds = new ArrayCollection();
        $this->units = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUsedFor(): ?string
    {
        return $this->used_for;
    }

    public function setUsedFor(string $used_for): self
    {
        $this->used_for = $used_for;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Guild>
     */
    public function getGuilds(): Collection
    {
        return $this->guilds;
    }

    public function addGuild(Guild $guild): self
    {
        if (!$this->guilds->contains($guild)) {
            $this->guilds->add($guild);
        }

        return $this;
    }

    public function removeGuild(Guild $guild): self
    {
        $this->guilds->removeElement($guild);

        return $this;
    }

    /**
     * @return Collection<int, Squadunit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Squadunit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
            $unit->setSquad($this);
        }

        return $this;
    }

    public function removeUnit(Squadunit $unit): self
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getSquad() === $this) {
                $unit->setSquad(null);
            }
        }

        return $this;
    }
}
