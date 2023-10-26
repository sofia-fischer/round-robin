<?php

namespace Tests\Unit;

use App\Services\PlanetXBoardGenerationService;
use App\ValueObjects\PlanetXBoard;
use PHPUnit\Framework\TestCase;

class PlanetXBoardGenerationTest extends TestCase
{
    /**
     * command: vendor/bin/phpunit --filter 'Tests\\Unit\\PlanetXBoardGenerationTest::testGeneration'  --repeat 3000
     */
    public function testGeneration()
    {
        $service = new PlanetXBoardGenerationService();
        $board = $service->generateBoard();

        foreach (PlanetXBoard::getStartingRules() as $rule) {
            $this->assertTrue($rule->isValid($board));
        }
    }
}
