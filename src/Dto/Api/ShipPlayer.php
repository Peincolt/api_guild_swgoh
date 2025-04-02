<?php

namespace App\Dto\Api;

use App\Dto\Api\UnitPlayer as UnitPlayerDto;

class ShipPlayer extends UnitPlayerDto
{
    /**
     * @param array<mixed> $apiUnitPlayerData
     */
    public function __construct(array $apiUnitPlayerData)
    {
        parent::__construct($apiUnitPlayerData);
    }
}