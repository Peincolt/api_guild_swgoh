<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class Ability
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly ?string $base_id;

    #[Assert\NotNull]
    #[Assert\Type("boolean")]
    public readonly ?bool $is_zeta;

    #[Assert\NotNull]
    #[Assert\Type("boolean")]
    public readonly ?bool $is_omega;

    #[Assert\NotNull]
    #[Assert\Type("boolean")]
    public readonly ?bool $is_omicron;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    public readonly ?string $description;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $omicron_mode;

    #[Assert\Length(max: 255)]
    public readonly ?string $character_base_id;

    // Propriétés présentent dans le retour API mais non utilisées pour le moment
    /*public readonly ?int $combat_type;
    public readonly ?bool $is_ultimate;
    /**
     * @var array<string>|null
     */
    /*public readonly ?array $omicron_battle_types;
    public readonly ?string $ship_base_id;
    public readonly ?string $ability_id;
    public readonly ?string $image;
    public readonly ?string $url;
    public readonly ?int $tier_max;
    public readonly ?string $character_base_id;*/

    /**
     * @param array<mixed> $apiAbilityData
     */
    public function __construct(array $apiAbilityData) {
        $defaults = [
            'name' => null,
            'base_id' => null,
            'is_zeta' => null,
            'is_omega' => null,
            'is_omicron' => null,
            'description' => null,
            'omicron_mode' => null,
            'character_base_id' => null
        ];
        $apiAbilityData = array_merge($defaults, $apiAbilityData);
        $this->name = $apiAbilityData['name'];
        $this->base_id = $apiAbilityData['base_id'];
        $this->is_zeta = $apiAbilityData['is_zeta'];
        $this->is_omega = $apiAbilityData['is_omega'];
        $this->is_omicron = $apiAbilityData['is_omicron'];
        $this->description = $apiAbilityData['description'];
        $this->omicron_mode = $apiAbilityData['omicron_mode'];
        $this->character_base_id = $apiAbilityData['character_base_id'];
    }
}