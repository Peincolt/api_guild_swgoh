<?php

namespace App\Validator\Constraints\Entity;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SquadValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        die('dedans');
    }
}