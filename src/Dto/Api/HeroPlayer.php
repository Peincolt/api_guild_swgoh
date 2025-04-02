<?php

namespace App\Dto\Api;

use App\Dto\Api\UnitPlayer as UnitPlayerDto;

class HeroPlayer extends UnitPlayerDto
{
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $gear_level;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public readonly ?int $relic_level;

    /**
     * @param array<mixed> $apiUnitPlayerData
     */
    public function __construct(array $apiUnitPlayerData)
    {
        $defaultsHeroPlayer = [
            'gear_level' => null,
            'relic_tier' => null,
        ];
        parent::__construct($apiUnitPlayerData);
        $mergeData = array_merge($defaultsHeroPlayer, $apiUnitPlayerData['data']);
        $this->gear_level = $mergeData['gear_level'];
        $this->relic_level = $mergeData['relic_tier'];
    }
}