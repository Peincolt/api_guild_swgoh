<?php

namespace App\Test\Dto\Api;

use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\Hero as HeroDto;

class HeroTest extends TestCase
{
    use DataTrait;

    private static ?array $heroData = [];
    
    public function testCreationHeroDto(): void
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }

        $heroDto = new HeroDto(self::$heroData);
        $this->assertSame("GRANDADMIRALTHRAWN", $heroDto->base_id);
        $this->assertSame("Grand Admiral Thrawn", $heroDto->name);
        $this->assertSame([
            "Leader",
            "Empire",
            "Fleet Commander"
        ], $heroDto->categories);
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png", $heroDto->image);
    }
}