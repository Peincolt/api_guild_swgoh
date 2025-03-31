<?php

namespace App\Tests\Utils\Manager;

use App\Entity\Ability;
use App\Entity\Hero;
use App\Repository\HeroRepository;
use App\Repository\AbilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Ability as AbilityManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;  
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbilityTest extends KernelTestCase
{
    private AbilityRepository $mockAbilityRepository;
    private HeroRepository $mockHeroRepository;
    private EntityManagerInterface $mockEntityManagerInterface;
    private SwgohGgApi $mockSwgogGgApi;
    private AbilityManager $abilityManager;
    private ValidatorInterface $validatorInterface;
    /**
     * @var string[]
     */
    private $baseSwgohggData;
    
    // On set up toutes les variables/mocks commun pour tous les tests
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->baseSwgohggData = 
        [
            "base_id" => "specialskill_VADER03",
            "ability_id" => "specialability_vader03",
            "name"=> "Merciless Massacre",
            "image"=> "https://game-assets.swgoh.gg/textures/tex.ability_darthvader_special03.png",
            "url"=> "/units/darth-vader/",
            "tier_max"=> 8,
            "is_zeta"=> true,
            "is_omega"=> false,
            "is_omicron"=> false,
            "is_ultimate"=> false,
            "description"=> "Gain Merciless and take a bonus turn after this one. All enemies gain Merciless Target, which can't be evaded or resisted. When Darth Vader uses an ability, Merciless Target is removed from the target enemy and Darth Vader takes a bonus turn. If the target did not have Merciless Target, Merciless expires and Merciless Target is removed from all remaining enemies. Darth Vader can ignore taunt effects when targeting enemies with Merciless Target.\\n\\nMerciless: +50% Offense (does not stack with Offense Up), +50% Critical Chance, and +50% Critical Damage; immune to Fear, Stun, and Turn Meter manipulation and Darth Vader's bonus turns do not trigger other characters' effects based on bonus turns or Turn Meter gain\\n\\nMerciless Target: Darth Vader must target a unit with this or a taunt effect, can ignore Taunt to target this unit, and takes a bonus turn after using an ability while targeting this unit",
            "combat_type"=> 1,
            "omicron_mode"=> 1,
            "type"=> 2,
            "character_base_id"=> "VADER",
            "ship_base_id"=> null,
            "omicron_battle_types"=> []
        ];
        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->mockAbilityRepository = $this->createMock(AbilityRepository::class);
        $this->mockHeroRepository = $this->createMock(HeroRepository::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockSwgogGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockEntityManagerInterface->method('persist')->willReturn(null);
        $this->mockEntityManagerInterface->method('flush')->willReturn(null);
        $this->abilityManager = new AbilityManager(
            $this->mockSwgogGgApi,
            $this->mockAbilityRepository,
            $this->mockHeroRepository,
            $this->mockEntityManagerInterface,
            $this->validatorInterface
        );
    }

    public function testFailSwgohggApi(): void
    {
        $errorSwgohApiData = [
            'error_code' => 500,
            'error_message_api_swgoh' => 'Jungle diff'
        ];

        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn($errorSwgohApiData);
        
        $caseSwgohggApiError = $this->abilityManager->updateAbilities();
        $this->assertSame($errorSwgohApiData, $caseSwgohggApiError);
    }

    public function testSchemUpdataSwgohggApi() :void
    {
        $errorUpdateSchemaApi = [
            'error_messages' => [
                'Erreur lors de la synchronisation de l\'abilité 0'
            ]
        ];
        $upateBaseSwgohggData = $this->baseSwgohggData;
        $upateBaseSwgohggData['ability_name'] = $upateBaseSwgohggData['name'];
        unset($upateBaseSwgohggData['name']);
        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn([$upateBaseSwgohggData]);
        $caseUpdateSchemaSwgohgg = $this->abilityManager->updateAbilities();
        $this->assertSame($errorUpdateSchemaApi, $caseUpdateSchemaSwgohgg);
    }

    public function testHeroMissing() :void
    {
        $missingHeroSwgohggData = $this->baseSwgohggData;
        $errorHeroMissing = [
            'error_messages' => [
                'Erreur lors de la synchronisation de l\'abilité 0. Le héro JHIN n\'existe pas dans la base de données'
            ]
        ];
        $missingHeroSwgohggData['character_base_id'] = "JHIN";
        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn([$missingHeroSwgohggData]);
        $this->mockHeroRepository->method('findBy')->willReturn(null);
        $caseHeroMissingSwgohgg = $this->abilityManager->updateAbilities();
        $this->assertSame($errorHeroMissing, $caseHeroMissingSwgohgg);
    }

    public function testEverythingIsFineWithoutAbility(): void
    {
        $ok = true;
        $heroMock = $this->createMock(Hero::class);
        $heroMock->method('getId')->willReturn(1);
        $heroMock->method('getId')->willReturn(1);
        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn([$this->baseSwgohggData]);
        $this->mockHeroRepository->method('findOneBy')->willReturn($heroMock);
        $caseEverythingIsFine = $this->abilityManager->updateAbilities();
        $this->assertTrue($caseEverythingIsFine);
    }

    public function testEverythingIsFineWithAbility(): void
    {
        $ok = true;
        $abilityMock = $this->createMock(Ability::class);
        $heroMock = $this->createMock(Hero::class);
        $abilityMock->method('getId')->willReturn(1);
        $heroMock->method('getId')->willReturn(1);
        $this->mockSwgogGgApi->method('fetchAbilities')
            ->willReturn([$this->baseSwgohggData]);
        $this->mockAbilityRepository->method('findOneBy')->willReturn($abilityMock);
        $this->mockHeroRepository->method('findOneBy')->willReturn($heroMock);
        $caseEverythingIsFineWithAbility = $this->abilityManager->updateAbilities();
        $this->assertTrue($caseEverythingIsFineWithAbility);
    }
}
