<?php

namespace App\Dto\Api;

abstract class UnitPlayer
{
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $level;
    
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $galactical_power;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $number_stars;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $protection;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $life;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $speed;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $combat_type;

    /**
     * @var array<string, mixed>
     */
    /*public array $defaults = [
        'level' => null,
        'power' => null,
        'rarity' => null,
        "combat_type" => null
    ];*/

    /**
     * @param array<mixed> $apiUnitPlayerData
     */
    public function __construct(array $apiUnitPlayerData)
    {
        $defaults = [
            'level' => null,
            'power' => null,
            'rarity' => null,
            "combat_type" => null
        ];
        $apiUnitPlayerData = array_merge($defaults, $apiUnitPlayerData['data']);
        $this->level = $apiUnitPlayerData['level'];
        $this->galactical_power = $apiUnitPlayerData['power'];
        $this->number_stars = $apiUnitPlayerData['rarity'];
        $this->speed = $apiUnitPlayerData['stats'][5] ?? null;
        $this->life = $apiUnitPlayerData['stats'][1] ?? null;
        $this->protection = $apiUnitPlayerData['stats'][28] ?? null;
        $this->combat_type = $apiUnitPlayerData['combat_type'];
    }
}