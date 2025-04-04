<?php

namespace App\Test\Dto\Api;

use App\Entity\Hero as HeroEntity;
use App\Dto\Api\Hero as HeroDto;
use App\Utils\Mapper\Hero as HeroMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeroTest extends KernelTestCase
{
    public function testHeroMapper(): void
    {
        self::bootKernel();
        $hero = new HeroEntity();

        $heroApiData = [
            "name"=> "Grand Admiral Thrawn",
            "base_id"=> "GRANDADMIRALTHRAWN",
            "url"=> "//swgoh.gg/units/grand-admiral-thrawn/",
            "image"=> "https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png",
            "power"=> 37507,
            "description"=> "Calculating Empire Leader who can halt enemies in their tracks, and grants Empire allies a new Special ability",
            "combat_type"=> 1,
            "gear_levels"=> [
                [
                    "tier"=> 1,
                    "gear"=> [
                        "003",
                        "002",
                        "009",
                        "004",
                        "011",
                        "005"
                    ]
                ],
                [
                    "tier"=> 2,
                    "gear"=> [
                        "016",
                        "028",
                        "032",
                        "034",
                        "020",
                        "028"
                    ]
                ],
                [
                    "tier"=> 3,
                    "gear"=> [
                        "055",
                        "050",
                        "048",
                        "055",
                        "054",
                        "050"
                    ]
                ],
                [
                    "tier"=> 4,
                    "gear"=> [
                        "071",
                        "055",
                        "068",
                        "054",
                        "050",
                        "026"
                    ]
                ],
                [
                    "tier"=> 5,
                    "gear"=> [
                        "083",
                        "080",
                        "088",
                        "068",
                        "054",
                        "050"
                    ]
                ],
                [
                    "tier"=> 6,
                    "gear"=> [
                        "095",
                        "095",
                        "090",
                        "091",
                        "083",
                        "102"
                    ]
                ],
                [
                    "tier"=> 7,
                    "gear"=> [
                        "107",
                        "108",
                        "108",
                        "095",
                        "088",
                        "102"
                    ]
                ],
                [
                    "tier"=> 8,
                    "gear"=> [
                        "117",
                        "111",
                        "102",
                        "108",
                        "100",
                        "095"
                    ]
                ],
                [
                    "tier"=> 9,
                    "gear"=> [
                        "111",
                        "117",
                        "108",
                        "099",
                        "109",
                        "098"
                    ]
                ],
                [
                    "tier"=> 10,
                    "gear"=> [
                        "131",
                        "135",
                        "129",
                        "140",
                        "130",
                        "136"
                    ]
                ],
                [
                    "tier"=> 11,
                    "gear"=> [
                        "143",
                        "131",
                        "111",
                        "117",
                        "108",
                        "150"
                    ]
                ],
                [
                    "tier"=> 12,
                    "gear"=> [
                        "162",
                        "163",
                        "158",
                        "169",
                        "167",
                        "G12Finisher_GRANDADMIRALTHRAWN_A"
                    ]
                ]
            ],
            "alignment"=> "Dark Side",
            "categories"=> [
                "Leader",
                "Empire",
                "Fleet Commander"
            ],
            "ability_classes"=> [
                "Stun",
                "+Defense",
                "Counter",
                "Speed Up",
                "Remove Turn Meter",
                "Dispel",
                "Bonus Turn",
                "Gain Turn Meter",
                "Speed Down",
                "+Tenacity",
                "Ability Block",
                "Defense Down",
                "+Speed"
            ],
            "role"=> "Support",
            "ship"=> "CAPITALCHIMAERA",
            "ship_slot"=> 0,
            "activate_shard_count"=> 145
        ];

        $heroDto = new HeroDto($heroApiData);
        $hero = HeroMapper::fromDto($hero, $heroDto);
        $this->assertSame("GRANDADMIRALTHRAWN", $hero->getBaseId());
        $this->assertSame("Grand Admiral Thrawn", $hero->getName());
        $this->assertSame([
            "Leader",
            "Empire",
            "Fleet Commander"
        ], $hero->getCategories());
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png", $hero->getImage());
    }
}