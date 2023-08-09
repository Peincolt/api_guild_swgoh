<?php

namespace App\Datasetssss;

use App\Entity\Squad;

dataset('squads_heroes_globals',[
    'Squad 1' => (new Squad())->setName('Hero 1')
    ->setUsedFor('attack')
    ->setType('hero')
    ->setUniqueIdentifier(rand(1000000,9999999))
]);

dataset('squads_ships_globals',[
    'Squad 1' => (new Squad())->setName('Ship 1')
    ->setUsedFor('attack')
    ->setType('ship')
    ->setUniqueIdentifier(rand(1000000,9999999))
]);