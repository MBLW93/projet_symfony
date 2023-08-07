<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Tax\CalculTva;

class TvaTest extends TestCase
{
    public function testSomething(): void
    {
        $calcul = new CalculTva();

        $result = $calcul->CalculTTC(10.0);

        $this->assertEquals(12.0, $result);

    }
}
