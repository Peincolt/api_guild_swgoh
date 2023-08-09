<?php

namespace App\Tests\Unit;

use App\Entity\Squad;

dataset('squads_local_heroes',[
    'Squad local 1' => (new Squad())->setName('Hero local 1')
    ->setUsedFor('attack')
    ->setType('hero')
    ->setUniqueIdentifier(rand(1000000,9999999))
]);