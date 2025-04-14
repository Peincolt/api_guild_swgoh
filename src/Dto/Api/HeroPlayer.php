<?php

namespace App\Dto\Api;

use App\Dto\Api\UnitPlayer as UnitPlayerDto;
use Symfony\Component\Validator\Constraints as Assert;

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
        if ($mergeData['gear_level'] == 13 && $mergeData['relic_tier'] == 2) {
            $this->relic_level = $mergeData['relic_tier'];
        } elseif ($mergeData['gear_level'] == 13 && $mergeData['relic_tier'] >= 3) {
            if (is_int($mergeData['relic_tier'])) {
                $this->relic_level = $mergeData['relic_tier'] - 2;
            }
        } else {
            $this->relic_level = 0;
        }
    }
}