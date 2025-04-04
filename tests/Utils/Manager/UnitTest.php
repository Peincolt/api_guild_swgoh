<?php

namespace App\Tests\Utils\Manager;

use App\Entity\Ship as ShipEntity;
use App\Utils\Manager\Unit as UnitManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use App\Utils\Factory\Unit as UnitFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnitTest extends KernelTestCase
{
    private EntityManagerInterface $mockEntityManagerInterface;
    private UnitFactory $mockUnitFactory;
    private SwgohGgApi $mockSwgohGg;
    private UnitManager $unitManager;
    private $apiShipsData = [
        [
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
        ]
    ];
    private array $apiHerosData = [
        [
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
        ]
    ];

    protected function setup(): void
    {
        $kernel = self::bootKernel();

        $this->mockSwgohGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockUnitFactory = $this->createMock(UnitFactory::class);
        $this->unitManager = new UnitManager(
            $this->mockEntityManagerInterface,
            $this->mockSwgohGgApi,
            $this->mockUnitFactory
        );

    }

    public function testFailSwgohggApi(): void
    {
        $errorSwgohApiData = [
            'error_code' => 500,
            'error_message_api_swgoh' => 'Mid diff'
        ];

        $this->mockSwgohGgApi->method('fetchHeroOrShip')
            ->willReturn($errorSwgohApiData);
        $caseSwgohggApiError = $this->unitManager->updateUnit("Hero");
        $this->assertEquals('Mid diff', $caseSwgohggApiError['error_message_api_swgoh']);
    }

    public function testSchemeUnitUpdate(): void
    {
        $updateApiShipData = $this->apiShipsData;
        unset($updateApiShipData[0]['base_id']);
        $errorUpdateSchemeApi = [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des unités. Cela est surement dû à un changement du format de l\'API'
        ];

        $this->mockSwgohGgApi->method('fetchHeroOrShip')
            ->willReturn($updateApiShipData);
        $this->mockUnitFactory->method('getEntityByApiResponse')->willReturn($errorUpdateSchemeApi);
        $caseSchemaUnitUpdate = $this->unitManager->updateUnit("Ship");
        $this->assertEquals($errorUpdateSchemeApi, $caseSchemaUnitUpdate);
    }

    public function testEverythingIsFine(): void
    {
        $this->mockSwgohGgApi->method('fetchHeroOrShip')
            ->willReturn($this->apiShipsData);
        $this->mockUnitFactory->method('getEntityByApiResponse')->willReturn((new ShipEntity()));
        $this->mockEntityManagerInterface->method('persist')->willReturn(null);
        $this->mockEntityManagerInterface->method('commit')->willReturn(null);
        $caseSchemaUnitUpdate = $this->unitManager->updateUnit("Ship");
        $this->assertEquals(true, $caseSchemaUnitUpdate);
    }

}