<?php

namespace App\Tests\Mapper;

use App\Dto\Api\ShipPlayer as ShipPlayerDto; 
use App\Entity\Player as PlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Utils\Mapper\ShipPlayer as ShipPlayerMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShipPlayerTest extends KernelTestCase
{
    public function testMapper() :void
    {
        self::bootKernel();

        $shipPlayer = new ShipPlayerEntity();
        $unit = $this->createMock(UnitEntity::class);
        $player = $this->createMock(PlayerEntity::class);
        $shipPlayerDto = new ShipPlayerDto(
            [
                'data' => [
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
            ]
        );

        $shipPlayer = ShipPlayerMapper::fromDto($shipPlayer, $shipPlayerDto, $player, $unit);

        $this->assertSame(85, $shipPlayer->getLevel());
        $this->assertSame(7, $shipPlayer->getNumberStars());
        $this->assertSame(84389, $shipPlayer->getGalacticalPower());
        $this->assertSame(0, $shipPlayer->getLife());
        $this->assertSame(0, $shipPlayer->getProtection());
        $this->assertSame(192, $shipPlayer->getSpeed());
    }
}