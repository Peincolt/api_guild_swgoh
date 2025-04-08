<?php

namespace App\Dto\Api;

use App\Dto\Api\Unit;
use Symfony\Component\Validator\Constraints as Assert;

class Ship extends Unit
{
    public function __construct(array $apiUnit)
    {
        parent::__construct($apiUnit);
    }
}