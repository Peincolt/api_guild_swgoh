<?php

namespace App\Test\Mapper;

use App\Dto\Api\Hero as HeroDto;
use App\Entity\Hero as HeroEntity;
use App\Tests\Trait\DataTrait;
use App\Utils\Mapper\Hero as HeroMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeroTest extends KernelTestCase
{
    use DataTrait;
    
    private static ?array $heroData = [];

    public function setUp(): void
    {
        if (empty(self::$heroData)) {
            $this->getData('Hero');
        }
    }

    public function testHeroMapper(): void
    {
        self::bootKernel();
        $hero = new HeroEntity();
        $heroDto = new HeroDto(self::$heroData);
        $hero = HeroMapper::fromDto($hero, $heroDto);
        $this->assertSame("GRANDADMIRALTHRAWN", $hero->getBaseId());
        $this->assertSame("Grand Admiral Thrawn", $hero->getName());
        $this->assertSame([
            "Leader",
            "Empire",
            "Fleet Commander"
        ], $hero->getCategories());
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_thrawn.png", $hero->getImage());
    }
}