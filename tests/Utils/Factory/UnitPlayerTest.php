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

    /**
     * @dataProvider errorMessages
     */
    public function testGetEntityByApiResponseErrorMessages($errorMessage, $heroPlayerData): void
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

    /*public function testMissingCombatType(): void
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
    }*/
}