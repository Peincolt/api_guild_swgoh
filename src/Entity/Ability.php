<?php

namespace App\Entity;

use App\Repository\AbilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbilityRepository::class)]
class Ability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $base_id = null;

    #[ORM\Column]
    private ?bool $is_zeta = null;

    #[ORM\Column]
    private ?bool $is_omega = null;

    #[ORM\Column]
    private ?bool $is_omicron = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $omicron_mode = null;

    #[ORM\ManyToOne(inversedBy: 'abilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hero $hero = null;

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

    public function getBaseId(): ?string
    {
        return $this->base_id;
    }

    public function setBaseId(string $base_id): self
    {
        $this->base_id = $base_id;

        return $this;
    }

    public function isIsZeta(): ?bool
    {
        return $this->is_zeta;
    }

    public function setIsZeta(bool $is_zeta): self
    {
        $this->is_zeta = $is_zeta;

        return $this;
    }

    public function isIsOmega(): ?bool
    {
        return $this->is_omega;
    }

    public function setIsOmega(bool $is_omega): self
    {
        $this->is_omega = $is_omega;

        return $this;
    }

    public function isIsOmicron(): ?bool
    {
        return $this->is_omicron;
    }

    public function setIsOmicron(bool $is_omicron): self
    {
        $this->is_omicron = $is_omicron;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOmicronMode(): ?int
    {
        return $this->omicron_mode;
    }

    public function setOmicronMode(?int $omicron_mode): self
    {
        $this->omicron_mode = $omicron_mode;

        return $this;
    }

    public function getHero(): ?Hero
    {
        return $this->hero;
    }

    public function setHero(?Hero $hero): self
    {
        $this->hero = $hero;

        return $this;
    }
}
