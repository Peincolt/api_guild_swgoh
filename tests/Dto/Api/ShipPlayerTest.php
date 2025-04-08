<?php

namespace App\Tests\Dto\Api;

use App\Dto\Api\ShipPlayer as DtoShipPlayer;
use PHPUnit\Framework\TestCase;

class ShipPlayerTest extends TestCase
{
    public function testShipPlayerCreation(): void
    {
        $fakeDataApi = [
            "data"=> [
                "base_id"=> "CAPITALEXECUTOR",
                "name"=> "Executor",
                "gear_level"=> 1,
                "level"=> 85,
                "power"=> 84389,
                "rarity"=> 7,
                "gear"=> [],
                "url"=> "/p/246639295/unit/CAPITALEXECUTOR/",
                "stats"=> [
                    "2"=> 11.7,
                    "3"=> 6.7,
                    "4"=> 6.7,
                    "1"=> 0,
                    "28"=> 0,
                    "5"=> 192.0,
                    "16"=> 1.5,
                    "17"=> 0.52159464,
                    "18"=> 0.67159464,
                    "27"=> 0,
                    "6"=> 10341.0,
                    "14"=> 24.458333333333336,
                    "10"=> null,
                    "37"=> 0,
                    "8"=> 0,
                    "12"=> 0,
                    "39"=> 0,
                    "7"=> 2085.0,
                    "15"=> 10.0,
                    "11"=> null,
                    "38"=> 0,
                    "9"=> 0,
                    "13"=> 0,
                    "40"=> 0,
                    "61"=> null
                ],
                "stat_diffs"=> [
                    "5"=> 74.0,
                    "17"=> 0.52159464,
                    "18"=> 0.52159464,
                    "6"=> 8215.0
                ],
                "zeta_abilities"=> [],
                "omicron_abilities"=> [],
                "ability_data"=> [
                    [
                        "id"=> "uniqueskill_CAPITALEXECUTOR01",
                        "ability_tier"=> 8,
                        "is_omega"=> false,
                        "is_zeta"=> false,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> false,
                        "name"=> "Vader's Bounty",
                        "tier_max"=> 8
                    ],
                    [
                        "id"=> "specialskill_CAPITALEXECUTOR03",
                        "ability_tier"=> 8,
                        "is_omega"=> false,
                        "is_zeta"=> false,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> false,
                        "name"=> "Something Special Planned",
                        "tier_max"=> 8
                    ],
                    [
                        "id"=> "specialskill_CAPITALEXECUTOR02",
                        "ability_tier"=> 8,
                        "is_omega"=> false,
                        "is_zeta"=> false,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> false,
                        "name"=> "We Only Need to Keep Them from Escaping",
                        "tier_max"=> 8
                    ],
                    [
                        "id"=> "specialskill_CAPITALEXECUTOR01",
                        "ability_tier"=> 8,
                        "is_omega"=> false,
                        "is_zeta"=> false,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> false,
                        "name"=> "Breach of Protocols",
                        "tier_max"=> 8
                    ],
                    [
                        "id"=> "basicskill_CAPITALEXECUTOR",
                        "ability_tier"=> 8,
                        "is_omega"=> false,
                        "is_zeta"=> false,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> false,
                        "name"=> "Unorthodox Methods",
                        "tier_max"=> 8
                    ]
                ],
                "mod_set_ids"=> [],
                "combat_type"=> 2,
                "relic_tier"=> null,
                "has_ultimate"=> false,
                "is_galactic_legend"=> false
            ]
        ];
        $dtoShipPlayer = new DtoShipPlayer($fakeDataApi);
        $this->assertSame(85, $dtoShipPlayer->level);
        $this->assertSame(7, $dtoShipPlayer->number_stars);
        $this->assertSame(84389, $dtoShipPlayer->galactical_power);
        $this->assertSame(0, $dtoShipPlayer->life);
        $this->assertSame(0, $dtoShipPlayer->protection);
        $this->assertSame(192, $dtoShipPlayer->speed);
        $this->assertSame(2, $dtoShipPlayer->combat_type);
    }
}