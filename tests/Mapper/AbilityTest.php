<?php

namespace App\Tests\Mapper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Ability as AbilityEntity;
use App\Dto\Api\Ability as AbilityDto;
use App\Mapper\Ability as AbilityMapper;

class AbilityTest extends KernelTestCase
{
    public function testAbilityMapper(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        //$abilityMapper = $container->get(AbilityMapper::class);

        $ability = new AbilityEntity();
        $abilityDto = new AbilityDto(
            [
                "name"=> "Merciless Massacre",
                "base_id" => "specialskill_VADER03",
                "is_zeta"=> true,
                "is_omega"=> false,
                "is_omicron"=> false,
                "description"=> "Gain Merciless and take a bonus turn after this one. All enemies gain Merciless Target, which can't be evaded or resisted. When Darth Vader uses an ability, Merciless Target is removed from the target enemy and Darth Vader takes a bonus turn. If the target did not have Merciless Target, Merciless expires and Merciless Target is removed from all remaining enemies. Darth Vader can ignore taunt effects when targeting enemies with Merciless Target.\\n\\nMerciless: +50% Offense (does not stack with Offense Up), +50% Critical Chance, and +50% Critical Damage; immune to Fear, Stun, and Turn Meter manipulation and Darth Vader's bonus turns do not trigger other characters' effects based on bonus turns or Turn Meter gain\\n\\nMerciless Target: Darth Vader must target a unit with this or a taunt effect, can ignore Taunt to target this unit, and takes a bonus turn after using an ability while targeting this unit",
                "omicron_mode"=> 1,
                "character_base_id"=> "VADER"
            ]
        );

        $mappedAbility = AbilityMapper::fromDto($ability, $abilityDto);

        $this->assertSame('specialskill_VADER03', $mappedAbility->getBaseId());
        $this->assertSame('Merciless Massacre', $mappedAbility->getName());
        $this->assertTrue($mappedAbility->isIsZeta());
        $this->assertFalse($mappedAbility->isIsOmega());
        $this->assertFalse($mappedAbility->isIsOmicron());
        $this->assertSame(
            "Gain Merciless and take a bonus turn after this one. All enemies gain Merciless Target, which can't be evaded or resisted. When Darth Vader uses an ability, Merciless Target is removed from the target enemy and Darth Vader takes a bonus turn. If the target did not have Merciless Target, Merciless expires and Merciless Target is removed from all remaining enemies. Darth Vader can ignore taunt effects when targeting enemies with Merciless Target.\\n\\nMerciless: +50% Offense (does not stack with Offense Up), +50% Critical Chance, and +50% Critical Damage; immune to Fear, Stun, and Turn Meter manipulation and Darth Vader's bonus turns do not trigger other characters' effects based on bonus turns or Turn Meter gain\\n\\nMerciless Target: Darth Vader must target a unit with this or a taunt effect, can ignore Taunt to target this unit, and takes a bonus turn after using an ability while targeting this unit", 
            $mappedAbility->getDescription()
        );
        $this->assertSame(1, $mappedAbility->getOmicronMode());
    }
}
