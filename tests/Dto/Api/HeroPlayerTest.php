<?php

namespace App\Tests\Dto\Api;

use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\HeroPlayer as DtoHeroPlayer;

class HeroPlayerTest extends TestCase
{
    use DataTrait;

    private static ?array $heroPlayerData = [];
    
    public function testHeroPlayerCreation(): void
    {
        if (empty(self::$heroPlayerData)) {
            $this->getData('HeroPlayer');
        }

        $dtoHeroPlayer = new DtoHeroPlayer(self::$heroPlayerData);
        $this->assertSame(85, $dtoHeroPlayer->level);
        $this->assertSame(7, $dtoHeroPlayer->number_stars);
        $this->assertSame(49710, $dtoHeroPlayer->galactical_power);
        $this->assertSame(113228, $dtoHeroPlayer->life);
        $this->assertSame(185355, $dtoHeroPlayer->protection);
        $this->assertSame(544, $dtoHeroPlayer->speed);
        $this->assertSame(1, $dtoHeroPlayer->combat_type);
    }
}