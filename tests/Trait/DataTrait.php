<?php

namespace App\Tests\Trait;

use App\Tests\Utils\JsonFileLoader;

Trait DataTrait
{
    private function getData(string $unitType): void
    {
        $variableName = lcfirst($unitType).'Data';
        
        if (!property_exists(self::class, $variableName)) {
            $this->fail('La propriété '.$variableName.' n\'existe pas');
        }

        if (empty(self::$$variableName)) {
            $unitData  = JsonFileLoader::getArrayFromJson(__DIR__ . '/../Data/'.$unitType.'.json');
            if (is_array($unitData )) {
                self::$$variableName  = $unitData;
                return;
            }
            $this->fail($unitData);
        }
    }
}