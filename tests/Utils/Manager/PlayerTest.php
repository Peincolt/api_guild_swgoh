<?php

namespace App\Tests\Utils\Manager;

use App\Entity\Guild;
use App\Dto\Api\PlayerDto;
use App\Entity\Player as PlayerEntity;
use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\Player as PlayerManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use Symfony\Component\Serializer\SerializerInterface;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerTest extends KernelTestCase
{
    use DataTrait;
    
    private Guild $mockGuild;
    private EntityManagerInterface $mockEntityManagerInterface;
    private ObjectRepository $mockObjectRepository;
    private PlayerManager $playerManager;
    private SwgohGgApi $mockSwgohGgApi;
    private SerializerInterface $serializer;
    private ValidatorInterface $validatorInterface;
    private static ?array $playerData = [];
    private string $allyCode = "xxxx";

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->serializer = $container->get(SerializerInterface::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockObjectRepository = $this->createConfiguredMock(ObjectRepository::class,
            [
                'findOneby' => null
            ]
        );
        $this->mockEntityManagerInterface->method('getRepository')->willReturn($this->mockObjectRepository);
        $this->mockSwgohGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockGuild = $this->createMock(Guild::class);
        $this->playerManager = new PlayerManager(
            $this->createMock(EntityManagerInterface::class),
            $this->mockSwgohGgApi,
            $this->serializer,
            $this->validatorInterface
        );
    }

    /**
     * @dataProvider updateGuildPlayerErrorMessages
     */
    public function testUpdateGuildPlayerErrorMessages(string $errorMessage, array $playerData, array $fetchData): void
    {
        $this->mockSwgohGgApi->method('fetchPlayer')->willReturn($fetchData);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($errorMessage);
        $this->playerManager
            ->updateGuildPlayer(
                $this->mockGuild,
                $playerData,
                $this->mockEntityManagerInterface
            );
    }

    /**
     * @dataProvider updateGuildPlayerEverythingIsNice
     */
    public function testUpdateGuildPlayerEverythingIsNice(array $playerData, array $fetchData, PlayerEntity $playerEntityDataProvider): void
    {
        $this->mockSwgohGgApi->method('fetchPlayer')->willReturn($fetchData);
        [$resultPlayerEntity, $resultPlayerData] = $this->playerManager
            ->updateGuildPlayer(
                $this->mockGuild,
                $playerData,
                $this->mockEntityManagerInterface
            );
        $this->assertSame($resultPlayerEntity->getName(), $playerEntityDataProvider->getName());
        $this->assertSame($resultPlayerEntity->getIdSwgoh(), $playerEntityDataProvider->getIdSwgoh());
        $this->assertSame($resultPlayerEntity->getGalacticalPower(), $playerEntityDataProvider->getGalacticalPower());
        $this->assertSame($resultPlayerEntity->getHeroesGalacticPower(), $playerEntityDataProvider->getHeroesGalacticPower());
        $this->assertSame($resultPlayerEntity->getShipsGalacticPower(), $playerEntityDataProvider->getShipsGalacticPower());
        $this->assertSame($resultPlayerEntity->getLevel(), $playerEntityDataProvider->getLevel());
        $this->assertSame($resultPlayerEntity->getGearGiven(), $playerEntityDataProvider->getGearGiven());
        $this->assertSame($resultPlayerEntity->getLastUpdate()->format('Y/m/d H:i'), $playerEntityDataProvider->getLastUpdate()->format('Y/m/d H:i'));
    }

    public function updateGuildPlayerEverythingIsNice(): array
    {
        if (empty(self::$playerData)) {
            $this->getData('Player');
        }

        return [
            [
                'playerData' => self::$playerData['data'],
                'fetchData' => self::$playerData,
                'playerEntityDataProvider' => $this->getPlayerEntity()
            ]
        ];
    }

    public function updateGuildPlayerErrorMessages(): array
    {
        if (empty(self::$playerData)) {
            $this->getData('Player');
        }

        $missingAllyCode = $missingData = self::$playerData;
        unset($missingAllyCode['data']['ally_code']);
        unset($missingData['data']['name']); 

        return [
            [
                'errorMessage' => 'Une erreur est survenue lors de la synchronisation des joueurs de la guilde. Une modification de l\'API a du être faite',
                'playerData' => $missingAllyCode['data'],
                'fetchData' => $missingAllyCode
            ],
            [
                'errorMessage' => 'Sup diff',
                'playerData' => self::$playerData['data'],
                'fetchData' => [
                    'error_message_api_swgoh' => 'Sup diff'
                ]
            ],
            [
                'errorMessage' => 'Erreur lors de la synchronisation des informations du joueur. Une modification de l\'API a du être faite',
                'playerData' => $missingData['data'],
                'fetchData' => $missingData
            ]
        ];
    }

    public function getPlayerEntity(): PlayerEntity
    {
        if (empty(self::$playerData)) {
            $this->getData('Player');
        }
        $date = new \DateTime(self::$playerData['data']['last_updated']);
        $player = new PlayerEntity();
        $player->setName(self::$playerData['data']['name'])
            ->setIdSwgoh(self::$playerData['data']['ally_code'])
            ->setGalacticalPower(self::$playerData['data']['galactic_power'])
            ->setHeroesGalacticPower(self::$playerData['data']['character_galactic_power'])
            ->setShipsGalacticPower(self::$playerData['data']['ship_galactic_power'])
            ->setLevel(self::$playerData['data']['level'])
            ->setGearGiven(self::$playerData['data']['guild_exchange_donations'])
            ->setLastUpdate($date);
        return $player;
    }
}