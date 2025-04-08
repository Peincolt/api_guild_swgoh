<?php

namespace App\Test\Dto\Api;

use App\Dto\Api\Ship as ShipDto;
use PHPUnit\Framework\TestCase;

class ShipTest extends TestCase
{
    public function testCreationShipDto(): void
    {
        $shipApiData = [
            "name"=> "Leviathan",
            "base_id"=> "CAPITALLEVIATHAN",
            "url"=> "//swgoh.gg/units/leviathan/",
            "image"=> "https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png",
            "power"=> 96996,
            "description"=> "Sith Capital Ship that takes over the enemy Capital Ship over the course of the battle",
            "combat_type"=> 2,
            "alignment"=> "Dark Side",
            "categories"=> [
                "Capital Ship",
                "Sith",
                "Sith Empire"
            ],
            "ability_classes"=> [
                "Gain Turn Meter",
                "+Speed",
                "Shock",
                "Bonus Turn",
                "Dispel",
                "Doubt",
                "Critical Hit Immunity",
                "Fear",
                "Remove Turn Meter",
                "Reset Cooldown",
                "Stun",
                "Counter",
                "AoE",
                "Taunt",
                "Breach Immunity",
                "Daze",
                "+Max Health",
                "Breach"
            ],
            "role"=> "Unknown",
            "capital_ship"=> true,
            "activate_shard_count"=> 80
        ];

        $shipDto = new ShipDto($shipApiData);
        $this->assertSame("CAPITALLEVIATHAN", $shipDto->base_id);
        $this->assertSame("Leviathan", $shipDto->name);
        $this->assertSame([
            "Capital Ship",
            "Sith",
            "Sith Empire"
        ], $shipDto->categories);
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png", $shipDto->image);
    }
}