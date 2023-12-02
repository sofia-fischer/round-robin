<?php

namespace Tests\Unit;

use App\Services\PlanetXBoardGenerationService;
use App\Services\PlanetXConferenceGenerationService;
use App\Services\PlanetXRuleGenerationService;
use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use PHPUnit\Framework\TestCase;

class PlanetXBoardGenerationTest extends TestCase
{
    /**
     * command: vendor/bin/phpunit --filter 'Tests\\Unit\\PlanetXBoardGenerationTest::testGeneration'  --repeat 3000
     */
    public function testGeneration()
    {
        $boardService = new PlanetXBoardGenerationService();
        $board = $boardService->generateBoard();

        foreach (PlanetXBoard::getStartingRules() as $rule) {
            $this->assertTrue($rule->isValid($board));
        }
    }

    /**
     * command: vendor/bin/phpunit --filter 'Tests\\Unit\\PlanetXBoardGenerationTest::testStartRules'  --repeat 3000
     */
    public function testStartRules()
    {
        $boardService = new PlanetXBoardGenerationService();
        $board = $boardService->generateBoard();
        $ruleService = new PlanetXRuleGenerationService();
        $startingRules = $ruleService->generateStartingRules($board, 6);

        foreach ($startingRules as $rule) {
            $this->assertTrue($rule->isValid($board));
        }
    }

    /**
     * command: vendor/bin/phpunit --filter 'Tests\\Unit\\PlanetXBoardGenerationTest::testGenerateConference'  --repeat 3000
     */
    public function testGenerateConference()
    {
        $boardService = new PlanetXBoardGenerationService();
        $board = $boardService->generateBoard();
        $conferenceService = new PlanetXConferenceGenerationService();
        $conferences = $conferenceService->generateRulesForConferences($board);

        $this->assertTrue($conferences->alpha->isValid($board));
        $this->assertTrue($conferences->beta->isValid($board));
        $this->assertTrue($conferences->gamma->isValid($board));
        $this->assertTrue($conferences->delta->isValid($board));
        $this->assertTrue($conferences->epsilon->isValid($board));
        $this->assertTrue($conferences->roh->isValid($board));
        $this->assertTrue($conferences->xConference->isValid($board));
        $this->assertTrue($conferences->xConference->icon === PlanetXIconEnum::PLANET_X);
    }
}
