<?php

namespace App\Utils\Manager;

use App\Tests\Trait\DataTrait;
use App\Entity\Guild as GuildEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\Guild as GuildManager;
use App\Utils\Manager\Player as PlayerManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use Symfony\Component\Serializer\SerializerInterface;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GuildTest extends KernelTestCase
{
    use DataTrait;
    
    private SwgohGgApi $mockSwgogGgApi;
    private EntityManagerInterface $mockEntityManagerInterface;
    private UnitPlayerManager $mockUnitPlayerManager;
    private PlayerManager $mockPlayerManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validatorInterface;
    
    private static ?array $guildData = [];

    // On set up toutes les variables/mocks commun pour tous les tests
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->mockSwgogGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockEntityManagerInterface = $this->createConfiguredMock(EntityManagerInterface::class,
            [
                'persist' => null,
                'flush' => null
            ]
        );
        $this->mockUnitPlayerManager = $this->createMock(UnitPlayerManager::class);
        $this->mockPlayerManager = $this->createMock(PlayerManager::class);
        $this->serializer = $container->get(SerializerInterface::class);
        $this->validatorInterface = $container->get(ValidatorInterface::class);

        $this->guildManager = new GuildManager(
            $this->mockSwgogGgApi,
            $this->mockEntityManagerInterface,
            $this->mockPlayerManager,
            $this->mockUnitPlayerManager,
            $this->serializer,
            $this->validatorInterface
        );
    }

    /**
     * FONCTIONS DE VERIFICATION DE LA FONCTION UpdateGuild
     */

    /**
     * @dataProvider updateGuildErrorMessages
     */
    public function testUpdateGuildErrorMessages(string $errorMessage, array $guildData): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($errorMessage);
        $this->guildManager
            ->updateGuild(
                'xxxx',
                $guildData,
                $this->mockEntityManagerInterface
            );
    }

    /**
     * @dataProvider updateGuildEverythingIsNice
     */
    public function testUpdateGuildEverythingIsNice(array $guildData, GuildEntity $guildDataProvider): void
    {
        $mockObjectRepository = $this->createConfiguredMock(ObjectRepository::class,
            [
                'findOneby' => null
            ]
        );
        $this->mockEntityManagerInterface->method('getRepository')->willReturn($mockObjectRepository);
        $result = $this->guildManager
            ->updateGuild(
                'xxxx',
                $guildData,
                $this->mockEntityManagerInterface
            );
        $this->assertInstanceOf(GuildEntity::class, $result);
        $this->assertSame($guildDataProvider->getName(), $result->getName());
        $this->assertSame($guildDataProvider->getIdSwgoh(), $result->getIdSwgoh());
        $this->assertSame($guildDataProvider->getGalacticPower(), $result->getGalacticPower());
    }

    public function updateGuildEverythingIsNice():array
    {
        if (empty(self::$guildData)) {
            $this->getData('Guild');
        }

        return [
            [
                'guildData' => self::$guildData,
                'guildDataProvider' => $this->getGuildEntity()
            ]
        ];
    }

    public function updateGuildErrorMessages(): array
    {
        if (empty(self::$guildData)) {
            $this->getData('Guild');
        }

        $missingDataField = $missingData = self::$guildData;
        unset($missingDataField['data']['guild_id']);
        unset($missingData['data']); 

        return [
            [
                'errorMessage' => 'Bot diff',
                'guildData' => [
                    'error_message_api_swgoh' => 'Bot diff'
                ]
            ],
            [
                'errorMessage' => 'Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite',
                'guildData' => $missingData
            ],
            [
                'errorMessage' => 'Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite',
                'guildData' => $missingDataField
            ]
        ];
    }

    public function getGuildEntity(): GuildEntity
    {
        $guild = new GuildEntity();
        $guild->setName("HGamers II")
            ->setIdSwgoh("uuwcpRBoStWfogZersAvJA")
            ->setGalacticPower(528125179)
            ->setNumberPlayers(49);
        return $guild;
    }
}