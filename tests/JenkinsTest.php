<?php

namespace App\tests;

use PHPUnit\Framework\TestCase;

class JenkinsTest extends TestCase
{
    public function testBon(): void
    {
        $this->assertSame("1","1");
    }

    public function testMauvais(): void
    {
        $this->assertSale('1', true);
    }
}