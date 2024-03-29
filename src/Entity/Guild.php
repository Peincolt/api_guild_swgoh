<?php

namespace App\Entity;

use App\Repository\GuildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\InformationTrait;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GuildRepository::class)]
class Guild
{
    use InformationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $galactic_power = null;

    #[ORM\Column]
    private ?int $number_players = null;

    #[ORM\OneToMany(mappedBy: 'guild', targetEntity: Player::class, orphanRemoval: true)]
    private Collection $players;

    #[ORM\ManyToMany(targetEntity: Squad::class, mappedBy: 'guilds')]
    private Collection $squads;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->squads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups('api_guild')]
    public function getGalacticPower(): ?string
    {
        return $this->galactic_power;
    }

    public function setGalacticPower(string $galactic_power): self
    {
        $this->galactic_power = $galactic_power;

        return $this;
    }

    #[Groups('api_guild')]
    public function getNumberPlayers(): ?int
    {
        return $this->number_players;
    }

    public function setNumberPlayers(int $number_players): self
    {
        $this->number_players = $number_players;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGuild($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGuild() === $this) {
                $player->setGuild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Squad>
     */
    public function getSquads(): Collection
    {
        return $this->squads;
    }

    public function addSquad(Squad $squad): self
    {
        if (!$this->squads->contains($squad)) {
            $this->squads->add($squad);
            $squad->addGuild($this);
        }

        return $this;
    }

    public function removeSquad(Squad $squad): self
    {
        if ($this->squads->removeElement($squad)) {
            $squad->removeGuild($this);
        }

        return $this;
    }
}
