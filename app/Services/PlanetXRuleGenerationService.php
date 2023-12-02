<?php

declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXRules\CountInFewSectorsRule;
use App\ValueObjects\PlanetXRules\CountInManySectorsRule;
use App\ValueObjects\PlanetXRules\InSectorRule;
use App\ValueObjects\PlanetXRules\NotInSectorRule;

class PlanetXRuleGenerationService
{
    /**
     * @param  \App\ValueObjects\PlanetXBoard  $board
     * @param  int  $count
     * @return array<\App\ValueObjects\PlanetXRules\PlanetXRule>
     */
    public function generateStartingRules(PlanetXBoard $board, int $count): array
    {
        $playerRules = [];

        while (count($playerRules) < $count) {
            // get a random sector
            $position = array_rand(range(0, 11));
            $sector = $board->getSector($position);
            // get the icons which are not in the sector
            $icons = PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X, $sector->getIcon()]);

            $newRule = new NotInSectorRule($icons[array_rand($icons)], $position);
            // check that rule is not already in the list
            foreach ($playerRules as $playerRule) {
                if ($playerRule->equals($newRule)) {
                    continue 2;
                }
            }

            foreach (PlanetXBoard::getStartingRules() as $startingRule) {
                if ($startingRule->equals($newRule)) {
                    continue 2;
                }
            }

            $playerRules[] = $newRule;
        }

        return $playerRules;
    }

    public function generateCountInSectorRule(PlanetXBoard $board, PlanetXIconEnum $icon, int $from, int $to): CountInFewSectorsRule|CountInManySectorsRule
    {
        $absolutTo = $to < $from ? $to + 12 : $to;
        $counter = 0;
        foreach (array_map(fn ($sector) => $sector % 12, range($from, $absolutTo)) as $index) {
            if ($board->getSector($index)->hasIcon($icon)) {
                $counter++;
            }
        }

        return ($absolutTo - $from) <= 3
            ? new CountInFewSectorsRule($icon, $from, $to, $counter)
            : new CountInManySectorsRule($icon, $from, $to, $counter);
    }

    public function generateInSectorRule(PlanetXBoard $board, int $index): InSectorRule
    {
        $realIcon = $board->getSector($index)->getIcon();
        $visibleIcon = $realIcon === PlanetXIconEnum::PLANET_X ? PlanetXIconEnum::EMPTY_SPACE : $realIcon;

        return new InSectorRule($visibleIcon, $index);
    }
}
