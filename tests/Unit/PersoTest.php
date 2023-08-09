<?php

namespace App\Tests\Unit;

use App\Repository\SquadRepository;
use App\Entity\Squad;

beforeEach(function () {
    $this->squadRepository = $this->getContainer()->get(SquadRepository::class);
});

it('test contains', function (string $capitalShip, string $rebelShip, string $otherShip) {
    expect($capitalShip)
        ->toBeIn(['Home One','Profundity'])
        ->and($rebelShip)
        ->toBeIn(['Faucon millénium','Y-Wing'])
        ->and($otherShip)
        ->toBeIn(['Ebon Hawk','Plo Koon Star Fighter']);
})->with('capital_ships')->with('rebel_ships')->with('other_ships')->skip('trop long');

it('test bidon',function
() {
    $value = 4;
    expect($value)->toBe(4);
});

//

it('test exception', function() {
    throw new \InvalidArgumentException('c\'est une Exception');
})->throwsIf(function(){
    return  true;
},\InvalidArgumentException::class,'c\'est une Exception');

dataset('capital_ships',['Home One','Profundity']);
dataset('rebel_ships',['Faucon millénium','Y-Wing']);
dataset('other_ships',['Ebon Hawk','Plo Koon Star Fighter']);