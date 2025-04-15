<?php

namespace App\Tests\Utils\Factory;

use App\Entity\Unit as UnitEntity;
use App\Repository\UnitRepository;
use App\Tests\Trait\DataTrait;
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
    use DataTrait;

    private EntityManagerInterface $entityManagerInterface;
    private EntityManagerInterface $mockEntityManagerInterface;
    private ObjectRepository $mockObjectRepository;
    private UnitPlayerRepository $mockUnitPlayerRepository;
    private UnitRepository $mockUnitRepository;
    private ValidatorInterface $validatorInterface;
    private UnitPlayerFactory $unitPlayerFactory;
    private PlayerEntity $mockPlayerEntity;
    private static ?array $heroPlayerData = [];
    private static ?array $shipPlayerData = [];

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockObjectRepository = $this->createMock(ObjectRepository::class);
        $this->mockUnitPlayerRepository = $this->createMock(UnitPlayerRepository::class);
        $this->mockUnitRepository = $this->createMock(UnitRepository::class);
        $this->mockPlayerEntity = $this->createConfiguredMock(
            PlayerEntity::class,
            [
                'getId' => 1,
                'getName' => 'William'
            ]
        );
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
     * Fonctions de configuration
     */
    public function everythingIsFine(): array
    {
        if (empty(self::$heroPlayerData)) {
            $this->getData('HeroPlayer');
        }

        if (empty(self::$shipPlayerData)) {
            $this->getData('ShipPlayer');
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
        if (empty(self::$heroPlayerData)) {
            $this->getData('HeroPlayer');
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