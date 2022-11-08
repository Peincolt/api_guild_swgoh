<?php

namespace App\Validator\Constraints\Entity;

use Symfony\Component\Validator\Constraint;

class Squad extends Constraint
{
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}