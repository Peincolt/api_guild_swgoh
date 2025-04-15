<?php

namespace App\Tests\Dto\Api;

use App\Entity\Guild;
use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\Player as PlayerDto;

class PlayerTest extends TestCase
{
    use DataTrait;

    private static ?array $playerData = [];
    
    public function testPlayerDtoCreation(): void
    {
        if (empty(self::$playerData)) {
            $this->getData('Player');
        }

        $dtoPlayer = new PlayerDto(self::$playerData);

        $this->assertSame(246639295, $dtoPlayer->id_swgoh);
        $this->assertSame("Wyøming", $dtoPlayer->name);
        $this->assertSame(85, $dtoPlayer->level);
        $this->assertSame(11426753, $dtoPlayer->galactic_power);
        $this->assertSame(6890288, $dtoPlayer->heroes_galactic_power);
        $this->assertSame(4536465, $dtoPlayer->ships_galactic_power);
        $this->assertSame(296, $dtoPlayer->gear_given);
        $this->assertSame("2025-04-06T20:51:54.574446", $dtoPlayer->last_updated);
    }
}