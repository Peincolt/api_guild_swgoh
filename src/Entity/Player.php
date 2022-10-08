<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\LevelPowerGalacticTrait;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    use LevelPowerGalacticTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_update = null;

    #[ORM\Column]
    private ?int $gear_given = null;

    #[ORM\Column]
    private ?int $ships_galactic_power = null;

    #[ORM\Column]
    private ?int $heros_gelactic_power = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Guild $guild = null;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: UnitPlayer::class, orphanRemoval: true)]
    private Collection $unitPlayers;

    public function __construct()
    {
        $this->unitPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->last_update;
    }

    public function setLastUpdate(\DateTimeInterface $last_update): self
    {
        $this->last_update = $last_update;

        return $this;
    }

    public function getGearGiven(): ?int
    {
        return $this->gear_given;
    }

    public function setGearGiven(int $gear_given): self
    {
        $this->gear_given = $gear_given;

        return $this;
    }

    public function getShipsGalacticPower(): ?int
    {
        return $this->ships_galactic_power;
    }

    public function setShipsGalacticPower(int $ships_galactic_power): self
    {
        $this->ships_galactic_power = $ships_galactic_power;

        return $this;
    }

    public function getHerosGelacticPower(): ?int
    {
        return $this->heros_gelactic_power;
    }

    public function setHerosGelacticPower(int $heros_gelactic_power): self
    {
        $this->heros_gelactic_power = $heros_gelactic_power;

        return $this;
    }

    public function getGuild(): ?Guild
    {
        return $this->guild;
    }

    public function setGuild(?Guild $guild): self
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * @return Collection<int, UnitPlayer>
     */
    public function getUnitPlayers(): Collection
    {
        return $this->unitPlayers;
    }

    public function addUnitPlayer(UnitPlayer $unitPlayer): self
    {
        if (!$this->unitPlayers->contains($unitPlayer)) {
            $this->unitPlayers->add($unitPlayer);
            $unitPlayer->setPlayer($this);
        }

        return $this;
    }

    public function removeUnitPlayer(UnitPlayer $unitPlayer): self
    {
        if ($this->unitPlayers->removeElement($unitPlayer)) {
            // set the owning side to null (unless already changed)
            if ($unitPlayer->getPlayer() === $this) {
                $unitPlayer->setPlayer(null);
            }
        }

        return $this;
    }
}
