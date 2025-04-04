<?php

namespace App\Tests\Utils\Factory;

use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\Player as PlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\UnitPlayerRepository;
use App\Repository\UnitRepository;
use App\Utils\Factory\UnitPlayer as UnitPlayerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitPlayerTest extends KernelTestCase
{
    private UnitPlayerRepository $mockUnitPlayerRepository;
    private UnitRepository $mockUnitRepository;
    private ValidatorInterface $validatorInterface;
    private UnitPlayerFactory $UnitPlayerFactory;
    private PlayerEntity $mockPlayerEntity;
    private array $heroPlayerData = [
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
    private array $shipPlayerData = [
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
    ];

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockUnitPlayerRepository = $this->createMock(UnitPlayerRepository::class);
        $this->mockUnitRepository = $this->createMock(UnitRepository::class);
        $this->mockPlayerEntity = $this->createMock(PlayerEntity::class);
        $this->mockPlayerEntity->method('getId')->willReturn(1);
        $this->unitPlayerFactory = new UnitPlayerFactory(
            $this->validatorInterface,
            $this->mockUnitPlayerRepository,
            $this->mockUnitRepository
        );
    }

    public function testMissingCombatType(): void
    {
        $errorMessageMissingCombatType = [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API'
        ];
        $updateShipPlayerData = $this->shipPlayerData;
        $updateShipPlayerData['data']['combat_type'] = 3;
        $caseMissingCombatType = $this->unitPlayerFactory->getEntityByApiResponse($updateShipPlayerData, $this->mockPlayerEntity);
        $this->assertSame($errorMessageMissingCombatType, $caseMissingCombatType);
    }

    public function testInvalidDto(): void
    {
        $wrongHeroPlayerData = $this->heroPlayerData;
        unset($wrongHeroPlayerData['data']['base_id']);
        $errorInvalidHeroDto = [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité du joueur. Cela est surement dû à un changement du format de l\'API'
        ];
        $caseInvalidDto = $this->unitPlayerFactory->getEntityByApiResponse($wrongHeroPlayerData, $this->mockPlayerEntity);
        $this->assertSame($errorInvalidHeroDto, $caseInvalidDto);
    }

    public function testMissingUnit(): void
    {
        $missingHeroPlayerData = $this->heroPlayerData;
        $missingHeroPlayerData['data']['base_id'] = 'MISSING';
        $errorMissingHero = [
            'error_message' => 'L\'unité MISSING n\'a pas été retrouvée dans la base de données. Veuillez mettre à jour les unités avant de mettre à jour les informations des joueurs.'
        ];
        $caseMissingUnit = $this->unitPlayerFactory->getEntityByApiResponse($missingHeroPlayerData, $this->mockPlayerEntity);
        $this->assertSame($errorMissingHero, $caseMissingUnit);
    }

    public function testEverythingIsFineForHero(): void
    {
        $this->mockUnitPlayerRepository->method('findOneBy')->willReturn((new HeroPlayerEntity()));
        $this->mockUnitRepository->method('findOneBy')->willReturn((new UnitEntity()));
        $caseEverythingIsFineForHero = $this->unitPlayerFactory->getEntityByApiResponse($this->heroPlayerData, $this->mockPlayerEntity);;
        $this->assertInstanceOf('\App\Entity\HeroPlayer', $caseEverythingIsFineForHero);
        $this->assertSame(85, $caseEverythingIsFineForHero->getLevel());
        $this->assertSame(7, $caseEverythingIsFineForHero->getNumberStars());
        $this->assertSame(49710, $caseEverythingIsFineForHero->getGalacticalPower());
        $this->assertSame(113228, $caseEverythingIsFineForHero->getLife());
        $this->assertSame(185355, $caseEverythingIsFineForHero->getProtection());
        $this->assertSame(544, $caseEverythingIsFineForHero->getSpeed());
        $this->assertSame(9, $caseEverythingIsFineForHero->getRelicLevel());
        $this->assertSame(13, $caseEverythingIsFineForHero->getGearLevel());
    }

    public function testEverythingIsFineForShip(): void
    {
        $this->mockUnitPlayerRepository->method('findOneBy')->willReturn((new ShipPlayerEntity()));
        $this->mockUnitRepository->method('findOneBy')->willReturn((new UnitEntity()));
        $caseEverythingIsFineForShip = $this->unitPlayerFactory->getEntityByApiResponse($this->shipPlayerData, $this->mockPlayerEntity);
        $this->assertInstanceOf('\App\Entity\ShipPlayer', $caseEverythingIsFineForShip);
        $this->assertSame(85, $caseEverythingIsFineForShip->getLevel());
        $this->assertSame(7, $caseEverythingIsFineForShip->getNumberStars());
        $this->assertSame(84389, $caseEverythingIsFineForShip->getGalacticalPower());
        $this->assertSame(0, $caseEverythingIsFineForShip->getLife());
        $this->assertSame(0, $caseEverythingIsFineForShip->getProtection());
        $this->assertSame(192, $caseEverythingIsFineForShip->getSpeed());
    }
}