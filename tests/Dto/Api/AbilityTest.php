<?php

namespace App\Tests\Dto\Api;

use App\Tests\Trait\DataTrait;
use App\Dto\Api\Ability as AbilityDto;
use PHPUnit\Framework\TestCase;

class AbilityTest extends TestCase
{
    use DataTrait;

    private static ?array $abilityData = [];

    public function testAbilityDtoCreation(): void
    {
        if (empty(self::$abilityData)) {
            $this->getData('Ability');
        }

        $dtoAbility = new AbilityDto(self::$abilityData);

        $this->assertSame('specialskill_VADER03', $dtoAbility->base_id);
        $this->assertSame('Merciless Massacre', $dtoAbility->name);
        $this->assertTrue($dtoAbility->is_zeta);
        $this->assertFalse($dtoAbility->is_omega);
        $this->assertFalse($dtoAbility->is_omicron);
        $this->assertSame(
            "Gain Merciless and take a bonus turn after this one. All enemies gain Merciless Target, which can't be evaded or resisted. When Darth Vader uses an ability, Merciless Target is removed from the target enemy and Darth Vader takes a bonus turn. If the target did not have Merciless Target, Merciless expires and Merciless Target is removed from all remaining enemies. Darth Vader can ignore taunt effects when targeting enemies with Merciless Target.\\n\\nMerciless: +50% Offense (does not stack with Offense Up), +50% Critical Chance, and +50% Critical Damage; immune to Fear, Stun, and Turn Meter manipulation and Darth Vader's bonus turns do not trigger other characters' effects based on bonus turns or Turn Meter gain\\n\\nMerciless Target: Darth Vader must target a unit with this or a taunt effect, can ignore Taunt to target this unit, and takes a bonus turn after using an ability while targeting this unit", 
            $dtoAbility->description
        );
        $this->assertSame(1, $dtoAbility->omicron_mode);
        $this->assertSame('VADER', $dtoAbility->character_base_id);
    }
}
