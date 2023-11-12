<?php

namespace App\Services;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXConferences;
use App\ValueObjects\PlanetXRules\InABandOfNSectorsRule;
use App\ValueObjects\PlanetXRules\NextToRule;
use App\ValueObjects\PlanetXRules\NotInSectorRule;
use App\ValueObjects\PlanetXRules\NotNextToRule;
use App\ValueObjects\PlanetXRules\NotWithinNSectorsRule;
use App\ValueObjects\PlanetXRules\WithinNSectorsRule;

class PlanetXConferenceGenerationService
{
    /**
     * @param  \App\ValueObjects\PlanetXBoard  $board
     * @param  int  $count
     * @return array<\App\ValueObjects\PlanetXRules\PlanetXRule>
     */
    public function generateRulesForBoard(PlanetXBoard $board, int $count): array
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

    public function generateRulesForConferences(PlanetXBoard $board): PlanetXConferences
    {
        // There is no "opposite of Rule". I felt like the UI Design would be too confusing
        // (aka my CSS skill is not high enough to place divs in a circle...)

        $rules = [];
        $icons = PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]);
        $startingrules = PlanetXBoard::getStartingRules();

        while (count($rules) < 6) {
            $newRuleIndex = array_rand([0,1,2,3,4]);

            $newRule = match ($newRuleIndex) {
                0 => $this->getNextToRule($board, $icons),
                1 => $this->getInABandOfNSectorsRule($board, $icons),
                2 => $this->getWithinNSectorsRule($board, $icons),
                3 => $this->getNotWithinNSectorsRule($board, $icons),
                4 => $this->getNotNextToRule($board, $icons),
            };

            if ($newRule === null) {
                continue;
            }

            foreach ($rules as $rule) {
                if ($rule->equals($newRule)) {
                    continue 2;
                }
            }

            foreach ($startingrules as $rule) {
                if ($rule->equals($newRule)) {
                    continue 2;
                }
            }

            $rules[] = $newRule;
        }

        $planetXRule = null;
        while ($planetXRule === null) {
            $newRule = match (array_rand([0, 1, 2])) {
                0 => $this->getInABandOfNSectorsRule($board, [PlanetXIconEnum::PLANET_X]),
                1 => $this->getWithinNSectorsRule($board, [PlanetXIconEnum::PLANET_X]),
                2 => $this->getNotWithinNSectorsRule($board, [PlanetXIconEnum::PLANET_X]),
            };

            if ($newRule === null) {
                continue;
            }

            $planetXRule = $newRule;
        }

        return new PlanetXConferences(
            alpha: $rules[0],
            beta: $rules[1],
            gamma: $rules[2],
            delta: $rules[3],
            epsilon: $rules[4],
            roh: $rules[5],
            xConference: $planetXRule,
        );
    }

    /**
     * @param  \App\ValueObjects\PlanetXBoard  $board
     * @param  array<PlanetXIconEnum>  $icons
     * @return \App\ValueObjects\PlanetXRules\NextToRule|null
     */
    private function getNextToRule(PlanetXBoard $board, array $icons): ?NextToRule
    {
        shuffle($icons);
        foreach ($icons as $icon) {
            foreach (PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]) as $nextToIcon) {
                $rule = new NextToRule($icon, $nextToIcon);

                if (! $rule->isValid($board)) {
                    continue;
                }

                return $rule;
            }
        }

        return null;
    }

    /**
     * @param  \App\ValueObjects\PlanetXBoard  $board
     * @param  array<PlanetXIconEnum>  $icons
     * @return \App\ValueObjects\PlanetXRules\InABandOfNSectorsRule
     */
    private function getInABandOfNSectorsRule(PlanetXBoard $board, array $icons): ?InABandOfNSectorsRule
    {
        shuffle($icons);
        foreach ($icons as $icon) {
            foreach ([5, 4, 3] as $band) {
                $rule = new InABandOfNSectorsRule($icon, $band);

                if (! $rule->isValid($board)) {
                    continue;
                }
                $probabilities = [0, 0, 0, 0, 0, 0, 1];
                $maybeMinimalBand = $band + $probabilities[array_rand($probabilities)];

                return new InABandOfNSectorsRule($icon, $maybeMinimalBand);
            }
        }

        return null;
    }

    private function getWithinNSectorsRule(PlanetXBoard $board, array $icons): ?WithinNSectorsRule
    {
        shuffle($icons);
        foreach ($icons as $icon) {
            foreach (PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]) as $otherIcon) {
                foreach ([5, 4, 3] as $within) {
                    $rule = new WithinNSectorsRule($icon, $within, $otherIcon);

                    if (! $rule->isValid($board)) {
                        continue;
                    }
                    $probabilities = [0, 0, 0, 0, 0, 0, 1];
                    $maybeMinimalWithin = $within + $probabilities[array_rand($probabilities)];

                    return new WithinNSectorsRule($icon, $maybeMinimalWithin, $otherIcon);
                }
            }
        }

        return null;
    }

    private function getNotWithinNSectorsRule(PlanetXBoard $board, array $icons): ?NotWithinNSectorsRule
    {
        shuffle($icons);
        foreach ($icons as $icon) {
            foreach (PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]) as $otherIcon) {
                foreach ([5, 4, 3] as $within) {
                    $rule = new NotWithinNSectorsRule($icon, $within, $otherIcon);

                    if (! $rule->isValid($board)) {
                        continue;
                    }
                    $probabilities = [0, 0, 0, 0, 0, 0, 1];
                    $maybeMinimalWithin = $within - $probabilities[array_rand($probabilities)];

                    return new NotWithinNSectorsRule($icon, $maybeMinimalWithin, $otherIcon);
                }
            }
        }

        return null;
    }

    private function getNotNextToRule(PlanetXBoard $board, array $icons)
    {
        shuffle($icons);
        foreach ($icons as $icon) {
            foreach (PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]) as $otherIcon) {
                $rule = new NotNextToRule($icon, $otherIcon);

                if (! $rule->isValid($board)) {
                    continue;
                }

                return $rule;
            }
        }

        return null;
    }
}
