<?php

namespace App\Tests\Utils\Manager;

use App\Entity\Hero;
use App\Entity\Ability;
use App\Repository\HeroRepository;
use App\Tests\Trait\DataTrait;
use App\Repository\AbilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Ability as AbilityManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;  
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbilityTest extends KernelTestCase
{
    use DataTrait;
    
    private static ?array $heroAbilityData = [];
    private static ?array $shipAbilityData = [];
    private AbilityRepository $mockAbilityRepository;
    private HeroRepository $mockHeroRepository;
    private EntityManagerInterface $mockEntityManagerInterface;
    private SwgohGgApi $mockSwgogGgApi;
    private AbilityManager $abilityManager;
    private ValidatorInterface $validatorInterface;
    
    // On set up toutes les variables/mocks commun pour tous les tests
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockAbilityRepository = $this->createMock(AbilityRepository::class);
        $this->mockHeroRepository = $this->createMock(HeroRepository::class);
        $this->mockSwgogGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockEntityManagerInterface = $this->createConfiguredMock(EntityManagerInterface::class,
            [
                'persist' => null,
                'flush' => null
            ]
        );
        $this->abilityManager = new AbilityManager(
            $this->mockSwgogGgApi,
            $this->mockAbilityRepository,
            $this->mockHeroRepository,
            $this->mockEntityManagerInterface,
            $this->validatorInterface
        );
    }

    /**
     * @dataProvider errorMessages
     */
    public function testUpdateAbilitiesErrorMessages(array $errorMessage, array $abilityData): void
    {
        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn($abilityData);
        
        $caseErrorMessages = $this->abilityManager->updateAbilities();
        $this->assertSame($errorMessage, $caseErrorMessages);
    }

    /**
     * @dataProvider everythingIsFine
     */
    public function testUpdateAbilitiesEverythingIsRight(array $abilityData): void
    {
        $abilityMock = $this->createConfiguredMock(
            Ability::class,
            [
                'getId' => 1
            ]
        );

        $heroMock = $this->createConfiguredMock(
            Hero::class,
            [
                'getId' => 1
            ]
        );

        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn($abilityData);

        $this->mockAbilityRepository->method('findOneBy')
            ->willReturn($abilityMock);

        $this->mockHeroRepository->method('findOneBy')
            ->willReturn($heroMock);

        $resultUpdateAbility = $this->abilityManager->updateAbilities();
        $this->assertTrue($resultUpdateAbility);
    }

    /**
     * Fonctions de configuration
     */

    public function errorMessages(): array
    {
        if (empty(self::$heroAbilityData)) {
            $this->getData('HeroAbility');
        }
        $wrongCombatType = $missingAttribute = $missingUnit = self::$heroAbilityData;
        unset($missingAttribute['name']);
        $wrongCombatType['combat_type'] = "54";
        $missingUnit['character_base_id'] = "JHIN";
        return [
            [
                'arrayErrorMessage' => [
                    'error_message_api_swgoh' => 'ERREUR',
                    'error_code' => 500
                ],
                'abilityData' => [
                    'error_message_api_swgoh' => 'ERREUR',
                    'error_code' => 500
                ]
            ],
            [
                'arrayErrorMessage' => [
                    'error_message' => 'Erreur lors de la synchronisation de l\'abilité 0. Une modification de l\'API a du être faite'
                ],
                'abilityData' => [$wrongCombatType]
            ],
            [
                'arrayErrorMessage' => [
                    'error_message' => 'Erreur lors de la synchronisation de l\'abilité 0'
                ],
                'abilityData' => [$missingAttribute]
            ],
            [
                'arrayErrorMessage' => [
                    'error_message' => 'Erreur lors de la synchronisation de l\'abilité 0. Le héro JHIN n\'existe pas dans la base de données'
                ],
                'abilityData' => [$missingUnit]
            ]
        ];
    }

    public function everythingIsFine(): array
    {
        if (empty(self::$heroAbilityData)) {
            $this->getData('HeroAbility');
        }

        if (empty(self::$shipAbilityData)) {
            $this->getData('ShipAbility');
        }

        return [
            [
                'abilityData' => [self::$heroAbilityData]
            ],
            [
                'abilityData' => [self::$shipAbilityData]
            ]
        ];
    }
}
