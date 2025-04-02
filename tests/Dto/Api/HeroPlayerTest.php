<?php

namespace App\Tests\Dto\Api;

use App\Dto\Api\HeroPlayer as DtoHeroPlayer;
use PHPUnit\Framework\TestCase;

class HeroPlayerTest extends TestCase
{
    public function testHeroPlayerCreation(): void
    {
        $fakeDataApi = [
            "data"=> [
                "base_id"=> "LORDVADER",
                "name"=> "Lord Vader",
                "gear_level"=> 13,
                "level"=> 85,
                "power"=> 49710,
                "rarity"=> 7,
                "gear"=> [
                    [
                        "slot"=> 0,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ],
                    [
                        "slot"=> 1,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ],
                    [
                        "slot"=> 2,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ],
                    [
                        "slot"=> 3,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ],
                    [
                        "slot"=> 4,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ],
                    [
                        "slot"=> 5,
                        "is_obtained"=> false,
                        "base_id"=> "9999"
                    ]
                ],
                "url"=> "/p/246639295/unit/LORDVADER/",
                "stats"=> [
                    "2"=> 20.2,
                    "3"=> 18.1,
                    "4"=> 18.1,
                    "1"=> 113228.0,
                    "28"=> 185355.0,
                    "5"=> 544.0,
                    "16"=> 1.92,
                    "17"=> 0.88145,
                    "18"=> 0.86803,
                    "27"=> 0.4,
                    "6"=> 10145.0,
                    "14"=> 89.93466666666666,
                    "10"=> 487.0,
                    "37"=> 0,
                    "8"=> 57.82335428382402,
                    "12"=> 0,
                    "39"=> 0,
                    "7"=> 7045.0,
                    "15"=> 19.768,
                    "11"=> 125.0,
                    "38"=> 0,
                    "9"=> 36.78730788299455,
                    "13"=> 0,
                    "40"=> 0,
                    "61"=> 60.0
                ],
                "stat_diffs"=> [
                    "1"=> 18780.0,
                    "28"=> 77590.0,
                    "5"=> 154.0,
                    "16"=> 0.41999999999999993,
                    "17"=> 0.19145,
                    "18"=> 0.12802999999999998,
                    "6"=> 1680.0,
                    "14"=> 9.768,
                    "8"=> 8.762227635801644,
                    "7"=> 1185.0,
                    "15"=> 9.768,
                    "9"=> 10.872142281600013
                ],
                "zeta_abilities"=> [
                    "basicskill_LORDVADER",
                    "specialskill_LORDVADER01",
                    "specialskill_LORDVADER02",
                    "leaderskill_LORDVADER",
                    "uniqueskill_LORDVADER01",
                    "uniqueskill_GALACTICLEGEND01"
                ],
                "omicron_abilities"=> [],
                "ability_data"=> [
                    [
                        "id"=> "basicskill_LORDVADER",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "Vindictive Storm",
                        "tier_max"=> 3
                    ],
                    [
                        "id"=> "specialskill_LORDVADER01",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "Dark Harbinger",
                        "tier_max"=> 3
                    ],
                    [
                        "id"=> "specialskill_LORDVADER02",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "Unshackled Emotions",
                        "tier_max"=> 3
                    ],
                    [
                        "id"=> "leaderskill_LORDVADER",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "My New Empire",
                        "tier_max"=> 3
                    ],
                    [
                        "id"=> "uniqueskill_LORDVADER01",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "Twisted Prophecy",
                        "tier_max"=> 3
                    ],
                    [
                        "id"=> "uniqueskill_GALACTICLEGEND01",
                        "ability_tier"=> 3,
                        "is_omega"=> false,
                        "is_zeta"=> true,
                        "is_omicron"=> false,
                        "has_omicron_learned"=> false,
                        "has_zeta_learned"=> true,
                        "name"=> "Galactic Legend",
                        "tier_max"=> 3
                    ]
                ],
                "mod_set_ids"=> [
                    "4",
                    "1"
                ],
                "combat_type"=> 1,
                "relic_tier"=> 9,
                "has_ultimate"=> true,
                "is_galactic_legend"=> true
            ]
        ];


        $dtoHeroPlayer = new DtoHeroPlayer($fakeDataApi);
        $this->assertSame(85, $dtoHeroPlayer->level);
        $this->assertSame(7, $dtoHeroPlayer->number_stars);
        $this->assertSame(49710, $dtoHeroPlayer->galactical_power);
        $this->assertSame(113228, $dtoHeroPlayer->life);
        $this->assertSame(185355, $dtoHeroPlayer->protection);
        $this->assertSame(544, $dtoHeroPlayer->speed);
        $this->assertSame(1, $dtoHeroPlayer->combat_type);
    }
}