<?php

namespace App\Tests\Utils\Factory;

use App\Entity\Unit as UnitEntity;
use App\Repository\UnitRepository;
use App\Entity\Player as PlayerEntity;
use App\Repository\UnitPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Utils\Factory\UnitPlayer as UnitPlayerFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitPlayerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManagerInterface;
    private ObjectRepository $mockObjectRepository;
    private UnitPlayerRepository $mockUnitPlayerRepository;
    private UnitRepository $mockUnitRepository;
    private UnitEntity $mockUnitEntity;
    private ValidatorInterface $validatorInterface;
    private UnitPlayerFactory $UnitPlayerFactory;
    private PlayerEntity $mockPlayerEntity;
    private string $projectDir;
    private static ?array $heroPlayerData = null;
    private static ?array $shipPlayerData = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->projetDir = $kernel->getProjectDir();
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockObjectRepository = $this->createMock(ObjectRepository::class);
        $this->mockUnitPlayerRepository = $this->createMock(UnitPlayerRepository::class);
        $this->mockUnitRepository = $this->createMock(UnitRepository::class);
        $this->mockPlayerEntity = $this->createMock(PlayerEntity::class);
        $this->mockedUnitEntity = $this->createMock(UnitEntity::class);
        $this->mockPlayerEntity->method('getId')->willReturn(1);
        $this->mockPlayerEntity->method('getName')->willReturn('William');
        $this->unitPlayerFactory = new UnitPlayerFactory(
            $this->validatorInterface,
            $this->mockUnitPlayerRepository,
            $this->mockUnitRepository,
        );
    }

    /**
     * @dataProvider errorMessages
     */
    public function testGetEntityByApiResponseErrorMessages(string $errorMessage, array $heroPlayerData): void
    {
        $this->mockObjectRepository
            ->method('findOneBy')
            ->willReturn(null);
        $this->mockEntityManagerInterface
            ->method('getRepository')
            ->with(UnitEntity::class)
            ->willReturn($this->mockObjectRepository);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($errorMessage);
        $this->unitPlayerFactory->getEntityByApiResponse($heroPlayerData, $this->mockPlayerEntity, $this->mockEntityManagerInterface);
    }

    /**
     * @dataProvider everythingIsFine
     */
    public function testGetEntityByApiResponseEverythingIsFineForHeroAndShip(array $unitData, UnitPlayerEntity $unitEntityPlayer, string $classType): void
    {
        $this->mockObjectRepository
            ->method('findOneBy')
            ->willReturnCallback(function ($params) {
                if (isset($params['base_id'])) {
                    return new UnitEntity();
                }

                if (isset($params['player']) && isset($params['unit'])) {
                    return null;
                }
            });
        $this->mockEntityManagerInterface
            ->method('getRepository')
            ->willReturnCallback(function ($class) {
                if ($class === UnitEntity::class || $class === UnitPlayerEntity::class) {
                    return $this->mockObjectRepository;
                }
            });

        $this->mockUnitPlayerRepository->method('findOneBy')->willReturn((new HeroPlayerEntity()));
        $this->mockUnitRepository->method('findOneBy')->willReturn((new UnitEntity()));
        $unitPlayer = $this->unitPlayerFactory->getEntityByApiResponse($unitData, $this->mockPlayerEntity, $this->mockEntityManagerInterface);
        $this->assertInstanceOf($classType, $unitPlayer);
        $this->assertSame($unitEntityPlayer->getLevel(), $unitPlayer->getLevel());
        $this->assertSame($unitEntityPlayer->getNumberStars(), $unitPlayer->getNumberStars());
        $this->assertSame($unitEntityPlayer->getGalacticalPower(), $unitPlayer->getGalacticalPower());
        $this->assertSame($unitEntityPlayer->getLife(), $unitPlayer->getLife());
        $this->assertSame($unitEntityPlayer->getProtection(), $unitPlayer->getProtection());
        $this->assertSame($unitEntityPlayer->getSpeed(), $unitPlayer->getSpeed());
        if ($classType === '\App\Entity\HeroPlayer') {
            $this->assertSame($unitEntityPlayer->getRelicLevel(), $unitPlayer->getRelicLevel());
            $this->assertSame($unitEntityPlayer->getGearLevel(), $unitPlayer->getGearLevel());
        }
    }

    /**
     * Fonction de configuration
     */

    private function getHeroPlayerData(): void
    {
        if (self::$heroPlayerData === null) {
            $filePathHeroPlayerData = __DIR__ . '/../../Data/HeroPlayer.json';
            $jsonHeroPlayerContent = file_get_contents($filePathHeroPlayerData);
            if ($jsonHeroPlayerContent !== false) {
                $this->assertNotEmpty($jsonHeroPlayerContent);
                $heroPlayerData = json_decode($jsonHeroPlayerContent, true);
                if ($heroPlayerData !== null) {
                    self::$heroPlayerData = $heroPlayerData;
                } else {
                    $this->fail('Une erreur est survenue lors du décodage des informations de LORD VADER');
                }
            } else {
                $this->fail("Impossible de lire les données contenant les informations de l'unité LORD VADER");
            }
        }
    }

    private function getShipPlayerData(): void
    {
        if (self::$shipPlayerData === null) {
            $filePathShipPlayerData = __DIR__ . '/../../Data/ShipPlayer.json';
            $jsonShipPlayerContent = file_get_contents($filePathShipPlayerData);
            if ($jsonShipPlayerContent !== false) {
                $shipPlayerData = json_decode($jsonShipPlayerContent, true);
                if ($shipPlayerData !== null) {
                    self::$shipPlayerData = $shipPlayerData;
                } else {
                    $this->fail('Une erreur est survenue lors du décodage des informations de l\'EXECUTOR');
                }
            }
        } else {
            $this->fail("Impossible de lire les données contenant les informations de l\'EXECUTOR");
        }
    }

    public function everythingIsFine(): array
    {
        if (self::$heroPlayerData === null) {
            $this->getHeroPlayerData();
        }

        if (self::$shipPlayerData === null) {
            $this->getShipPlayerData();
        }

        return [
            [
                'unitData' => self::$heroPlayerData,
                'unitPlayerEntity' => $this->getHeroPlayerEntity(),
                'classType' => '\App\Entity\HeroPlayer'
            ],
            [
                'unitData' => self::$shipPlayerData,
                'unitPlayerEntity' => $this->getShipPlayerEntity(),
                'classType' => '\App\Entity\ShipPlayer'
            ]
        ];
    }


    public function errorMessages(): array
    {
        if (self::$heroPlayerData === null) {
            $this->getHeroPlayerData();
        }
        $wrongCombatType = $missingAttribute = $missingUnit = self::$heroPlayerData;
        unset($missingAttribute['data']['base_id']);
        $missingUnit['data']['base_id'] = "DARTH JAR JAR";
        $wrongCombatType['data']['combat_type'] = "54";
        return [
            [
                'errorMessage' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API',
                'heroPlayerData' => $wrongCombatType
            ],
            [
                'errorMessage' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité du joueur William. Cela est surement dû à un changement du format de l\'API',
                'heroPlayerData' => $missingAttribute
            ],
            [
                'errorMessage' => 'L\'unité DARTH JAR JAR n\'a pas été retrouvée dans la base de données. Veuillez mettre à jour les unités avant de mettre à jour les informations des joueurs.',
                'heroPlayerData' => $missingUnit
            ]
        ];
    }
    
    private function getShipPlayerEntity(): ShipPlayerEntity
    {
        $shipPlayerEntity = new ShipPlayerEntity();
        $shipPlayerEntity->setLevel(85)
            ->setNumberStars(7)
            ->setGalacticalPower(84389)
            ->setLife(0)
            ->setProtection(0)
            ->setSpeed(192);
        return $shipPlayerEntity;
    }

    private function getHeroPlayerEntity(): HeroPlayerEntity
    {
        $heroPlayerEntity = new HeroPlayerEntity();
        $heroPlayerEntity->setLevel(85)
            ->setNumberStars(7)
            ->setGalacticalPower(49710)
            ->setLife(113228)
            ->setProtection(185355)
            ->setSpeed(544)
            ->setRelicLevel(9)
            ->setGearLevel(13);
        return $heroPlayerEntity;
    }
}