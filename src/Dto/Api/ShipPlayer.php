<?php

namespace App\Dto\Api;

use App\Dto\Api\UnitPlayer as UnitPlayerDto;
use Symfony\Component\Validator\Constraints as Assert;

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