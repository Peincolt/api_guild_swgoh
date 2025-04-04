<?php

namespace App\Tests\Utils\Factory;

use App\Repository\UnitRepository;
use App\Utils\Factory\Unit as UnitFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitTest extends KernelTestCase
{
    private UnitRepository $mockUnitRepository;
    private ValidatorInterface $validatorInterface;
    private UnitFactory $unitFactory;
    private array $apiHeroData = [
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
    private array $apiShipData = [
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

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockUnitRepository = $this->createMock(UnitRepository::class);
        $this->unitFactory = new UnitFactory(
            $this->validatorInterface,
            $this->mockUnitRepository
        );
    }

    public function testWrongClassName(): void
    {
        $wrongClassName = 'Gift';
        $errorWrongClassName = [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des unités. Cela est surement dû à un changement du format de l\'API'
        ];
        $caseWrongClassName = $this->unitFactory->getEntityByApiResponse($this->apiHeroData, $wrongClassName);
        $this->assertSame($errorWrongClassName, $caseWrongClassName);
    }

    public function testInvalidDto(): void
    {
        $wrongUnitData = [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API'
        ];
        $wrongShipData = $this->apiShipData;
        unset($wrongShipData['base_id']);
        $caseWrongShipData = $this->unitFactory->getEntityByApiResponse($wrongShipData, "Hero");
        $this->assertSame($wrongUnitData, $caseWrongShipData);
    }

    public function testEverythingIsFineHero(): void
    {
        $caseEverythingIsFineForHero = $this->unitFactory->getEntityByApiResponse($this->apiHeroData, "Hero");
        $this->assertInstanceOf('\App\Entity\Hero', $caseEverythingIsFineForHero);
        $this->assertSame("Grand Admiral Thrawn", $caseEverythingIsFineForHero->getName());
        $this->assertSame("GRANDADMIRALTHRAWN", $caseEverythingIsFineForHero->getBaseId());
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png", $caseEverythingIsFineForHero->getImage());
        $this->assertSame([
            "Leader",
            "Empire",
            "Fleet Commander"
        ], $caseEverythingIsFineForHero->getCategories());
    }

    public function testEverythingIsFineShip(): void
    {
        $caseEverythingIsFineForShip = $this->unitFactory->getEntityByApiResponse($this->apiShipData, "Ship");
        $this->assertInstanceOf('\App\Entity\Ship', $caseEverythingIsFineForShip);
        $this->assertSame("Leviathan", $caseEverythingIsFineForShip->getName());
        $this->assertSame("CAPITALLEVIATHAN", $caseEverythingIsFineForShip->getBaseId());
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png", $caseEverythingIsFineForShip->getImage());
        $this->assertSame([
            "Capital Ship",
            "Sith",
            "Sith Empire"
        ], $caseEverythingIsFineForShip->getCategories());
    }
}