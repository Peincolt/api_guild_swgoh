<?php

namespace App\Tests\Utils\Factory;

use App\Entity\Hero as HeroEntity;
use App\Entity\Ship as ShipEntity;
use App\Entity\Unit as UnitEntity;
use App\Repository\UnitRepository;
use App\Tests\Trait\DataTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Factory\Unit as UnitFactory;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitTest extends KernelTestCase
{
    use DataTrait;

    private UnitRepository $mockUnitRepository;
    private EntityManagerInterface $mockEntityManagerInterface;
    private ObjectRepository $mockObjectRepository;
    private ValidatorInterface $validatorInterface;
    private UnitFactory $unitFactory;
    private static ?array $heroData = [];
    private static ?array $shipData = [];

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockUnitRepository = $this->createMock(UnitRepository::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockObjectRepository = $this->createMock(ObjectRepository::class);
        $this->unitFactory = new UnitFactory(
            $this->validatorInterface,
            $this->mockUnitRepository
        );
    }

    /**
     * @dataProvider errorMessages
     */
    public function testGetEntityByApiResponseErrorMessages(array $unitData, string $classeName, string $errorMessage): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($errorMessage);
        $this->unitFactory->getEntityByApiResponse($unitData, $classeName, $this->mockEntityManagerInterface);
    }

    /**
     * @dataProvider everythingIsFine
     */
    public function testGetEntityByApiResponseEverythingIsFine(
        array $unitData,
        string $className,
        string $fullClassName,
        UnitEntity $unitEntity
    ): void {

        $this->mockEntityManagerInterface->method('getRepository')
            ->with(UnitEntity::class)
            ->willReturn($this->mockObjectRepository);
        $this->mockObjectRepository->method('findOneBy')
            ->willReturn(null);
        $unit = $this->unitFactory->getEntityByApiResponse($unitData, $className, $this->mockEntityManagerInterface);
        $this->assertInstanceOf($unitEntity::class, $unit);
        $this->assertSame($unitEntity->getName(), $unit->getName());
        $this->assertSame($unitEntity->getBaseId(), $unit->getBaseId());
        $this->assertSame($unitEntity->getImage(), $unit->getImage());
        $this->assertSame($unitEntity->getCategories(), $unit->getCategories());
    }

    /**
     * Fonctions de configuration
     */

    public function errorMessages(): array
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }

        $wrongHeroData = self::$heroData;
        unset($wrongHeroData['name']);
        
        return [
            [
                'unitData' => self::$heroData,
                'className' => 'WRONG',
                'error_message' => 'Une erreur est survenue lors de la mise à jour des unités. Cela est surement dû à un changement du format de l\'API'
            ],
            [
                'unitData' => $wrongHeroData,
                'className' => 'Hero',
                'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API'
            ]
        ];
    }

    public function everythingIsFine(): array
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }

        if (empty(self::$shipData)) {
            $this->getData('Ship');
        }

        return [
            [
                'unitData' => self::$shipData,
                'className' => 'Ship',
                'fullClassName' => '\App\Entity\Ship',
                'unitEntity' => $this->getShipEntity()
            ],
            [
                'unitData' => self::$heroData,
                'className' => 'Hero',
                'fullClassName' => '\App\Entity\Hero',
                'unitEntity' => $this->getHeroEntity()
            ]
        ];
    }

    private function getShipEntity(): ShipEntity
    {
        $shipEntity = new ShipEntity();
        $shipEntity->setName("Leviathan")
            ->setBaseId("CAPITALLEVIATHAN")
            ->setImage("https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png")
            ->setCategories([
                "Capital Ship",
                "Sith",
                "Sith Empire"
            ]);
        return $shipEntity;
    }

    private function getHeroEntity(): HeroEntity
    {
        $heroEntity = new HeroEntity();
        $heroEntity->setName("Grand Admiral Thrawn")
            ->setBaseId("GRANDADMIRALTHRAWN")
            ->setImage("https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png")
            ->setCategories([
                "Leader",
                "Empire",
                "Fleet Commander"
            ]);
        return $heroEntity;
    }
}