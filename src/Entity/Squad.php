<?php

namespace App\Entity;

use App\Repository\SquadRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SquadRepository::class)]
#[UniqueEntity('unique_identifier')]
#[ORM\HasLifecycleCallbacks]
class Squad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Vous devez renseigner cette valeur')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Vous devez renseigner cette valeur')]
    #[Assert\Choice(choices: ['defense', 'attack'], message: 'Seul les valeurs "attack" ou "defense" sont acceptées.')]
    private ?string $used_for = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['hero', 'ship'], message: 'Seul les valeurs "hero" ou "ship" sont acceptées.')]
    #[Assert\NotBlank(message: 'Vous devez renseigner cette valeur')]
    private ?string $type = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $unique_identifier = null;

    #[ORM\ManyToMany(targetEntity: Guild::class, inversedBy: 'squads')]
    private Collection $guilds;

    #[ORM\OneToMany(mappedBy: 'squad', targetEntity: SquadUnit::class, orphanRemoval: true)]
    #[ORM\OrderBy(['show_order' => 'ASC'])]
    private Collection $units;

    #[ORM\ManyToOne(inversedBy: 'squads')]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->guilds = new ArrayCollection();
        $this->units = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['api_squad'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Groups(['api_squad'])]
    public function getUsedFor(): ?string
    {
        return $this->used_for;
    }

    public function setUsedFor(string $used_for): self
    {
        $this->used_for = $used_for;

        return $this;
    }

    #[Groups(['api_squad'])]
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    
    #[Groups(['api_squad'])]
    public function getUniqueIdentifier(): ?string
    {
        return $this->unique_identifier;
    }

    public function setUniqueIdentifier(string $unique_identifier): self
    {
        $this->unique_identifier = $unique_identifier;

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
     * @return Collection<int, SquadUnit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }
    
    public function setUnits(Collection $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function addUnit(SquadUnit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
            $unit->setSquad($this);
        }

        return $this;
    }

    public function removeUnit(SquadUnit $unit): self
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getSquad() === $this) {
                $unit->setSquad(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function setUniqueIdentifierValue(): void
    {
        $true = true;
        $bytes = openssl_random_pseudo_bytes(20, $true);
        $this->unique_identifier = bin2hex($bytes);
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
