<?php

namespace App\Tests\Utils\Manager;

use App\Tests\Trait\DataTrait;
use App\Entity\Ship as ShipEntity;
use App\Entity\Unit as UnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Factory\Unit as UnitFactory;
use App\Utils\Manager\Unit as UnitManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnitTest extends KernelTestCase
{
    use DataTrait;
    
    private EntityManagerInterface $mockEntityManagerInterface;
    private UnitFactory $mockUnitFactory;
    private SwgohGgApi $mockSwgohGg;
    private UnitManager $unitManager;
    private static ?array $shipData;
    private static ?array $heroData;

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

    /**
     * @dataProvider errorMessages
     */
    public function testUpdateUnitErrorMessages(array $fetchData, string $exceptionMessage, array $result): void
    {
        $this->mockSwgohGgApi->method('fetchHeroOrShip')
            ->willReturn($fetchData);
        $this->mockUnitFactory->method('getEntityByApiResponse')
            ->willThrowException(new \Exception($exceptionMessage));
        $errorUnit = $this->unitManager->updateUnit("Hero");
        $this->assertSame($result, $errorUnit);
    }

    /**
     * @dataProvider everythingIsFine
     */
    public function testUpdateUnitEverythingIsFine(array $fetchData): void
    {
        $this->mockSwgohGgApi->method('fetchHeroOrShip')
            ->willReturn($fetchData);
        $this->mockUnitFactory->method('getEntityByApiResponse')
            ->willReturn(new UnitEntity());
        $unit = $this->unitManager->updateUnit("Hero");
        $this->assertSame(true, $unit);
    }

    public function everythingIsFine(): array
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }

        return [
            [
                'fetchData' => [self::$heroData]
            ]
        ];
    }

    public function errorMessages(): array
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }

        return [
            [
                'fetchData' => [
                    'error_message_api_swgoh' => 'Jgl diff'
                ],
                'exceptionMessage' => '',
                'result' => [
                    'error_message_api_swgoh' => 'Jgl diff'
                ]
            ],
            [
                'fetchData' => [self::$heroData],
                'exceptionMessage' => 'Message soulevé par UnitFactory',
                'result' => [
                    'error_message' => 'Message soulevé par UnitFactory'
                ]
            ]
        ];
    }

    /*public function testSchemeUnitUpdate(): void
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
    }*/

}