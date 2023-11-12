<?php

namespace App\Services;

use App\Exceptions\PlanetXBoardGenerationException;
use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class PlanetXBoardGenerationService
{
    public function generateBoard(): PlanetXBoard
    {
        $board = PlanetXBoard::emptyBoard();

        // So... there are 2.176.782.336 possible boards.
        // In University, I learned multiple algorithms to deal with Constraint satisfaction problems
        // While working I learned, that the moment you have to use those, your problem is too big for a web developer

        // randomly place planet x
        $board->getSector(array_rand(range(0, 11)))->setIconOrFail(PlanetXIconEnum::PLANET_X, true);

        // down to 48.828.125 possible boards
        // randomly place the asteroids
        $board = $this->placeMoons($board);

        // down to 262.144 possible boards
        // randomly place the asteroids
        $board = $this->placeAsteroids($board);
        $board = $this->placeAsteroids($board);

        // down to 243 possible boards
        // randomly place the dwarf planet
        $board = $this->placeDwarfPlanet($board);

        // down to 16 possible boards
        // randomly place the clouds
        $board = $this->placeGalaxiesAndEmptySpaces($board);

        return $board;
    }

    private function placeMoons(PlanetXBoard $board): PlanetXBoard
    {
        $allowedIndexes = [1, 2, 4, 6, 10];
        $planetXSector = $board->getSectorIndexesWithIcon(PlanetXIconEnum::PLANET_X)[0];
        $allowedIndexes = array_diff($allowedIndexes, [$planetXSector]);

        $asteroidIndex = $allowedIndexes[array_rand($allowedIndexes)];
        $board->getSector($asteroidIndex)->setIconOrFail(PlanetXIconEnum::MOON, true);
        $allowedIndexes = array_diff($allowedIndexes, [$asteroidIndex]);

        // If both asteroids are placed two away from the planet x, the dwarf planet can't be placed there.
        // The dwarf planet and one gas cloud are the only icons that can be placed in "Single Sectors"
        if (($planetXSector + 2) % 12 === $asteroidIndex) {
            $allowedIndexes = array_diff($allowedIndexes, [($planetXSector + 10) % 12]);
        }
        if (($planetXSector + 10) % 12 === $asteroidIndex) {
            $allowedIndexes = array_diff($allowedIndexes, [($planetXSector + 2) % 12]);
        }

        $asteroidIndex2 = $allowedIndexes[array_rand($allowedIndexes)];
        $board->getSector($asteroidIndex2)->setIconOrFail(PlanetXIconEnum::MOON, true);

        return $board;
    }

    private function placeAsteroids(PlanetXBoard $board)
    {
        $emptyIndexes = $board->getSectorIndexesWithIcon();
        // shuffle the indexes to avoid always placing the asteroids in the same order
        shuffle($emptyIndexes);

        foreach ($emptyIndexes as $emptySector) {
            $nextSector = ($emptySector + 1) % 12;

            if ($board->hasAnyIcon($nextSector)) {
                continue;
            }

            $futureBoard = PlanetXBoard::fromArray($board->toArray());
            $futureBoard->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::ASTEROID, true);
            $futureBoard->getSector($nextSector)->setIconOrFail(PlanetXIconEnum::ASTEROID, true);

            if (count($futureBoard->getSingleSectorIndexesWithoutIcon()) > 2) {
                continue;
            }

            // check if the dwarf planet can be placed
            try {
                $this->placeDwarfPlanet($futureBoard);
            } catch (PlanetXBoardGenerationException $exception) {
                continue;
            }

            $board->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::ASTEROID, true);
            $board->getSector($nextSector)->setIconOrFail(PlanetXIconEnum::ASTEROID, true);

            return $board;
        }

        throw new PlanetXBoardGenerationException('Could not place asteroids', $board->toArray());
    }

    private function placeDwarfPlanet(PlanetXBoard $board): PlanetXBoard
    {
        // the situation to avoid are two sectors with an empty sector between them
        // if two of those "single sectors" exist, the asteroids and clouds can't be placed
        $emptySectorsBetweenTwo = $board->getSingleSectorIndexesWithoutIcon();
        $planetXSector = $board->getSectorIndexesWithIcon(PlanetXIconEnum::PLANET_X)[0];

        // if we already have two single sectors, the dwarf planet must be placed in one of them
        if (count($emptySectorsBetweenTwo) > 1) {
            foreach ($emptySectorsBetweenTwo as $emptySector) {
                if (($emptySector + 1) % 12 === $planetXSector || ($emptySector + 11) % 12 === $planetXSector) {
                    continue;
                }

                $board->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::PLANET, true);

                return $board;
            }

            throw new PlanetXBoardGenerationException('Could not place dwarf planet in single sector', $board->toArray());
        }

        foreach ($board->getSectorIndexesWithIcon() as $emptySector) {
            if (($emptySector + 1) % 12 === $planetXSector || ($emptySector + 11) % 12 === $planetXSector) {
                continue;
            }

            $futureBoard = PlanetXBoard::fromArray($board->toArray());
            $futureBoard->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::PLANET, true);

            if (count($futureBoard->getSingleSectorIndexesWithoutIcon()) > 1) {
                continue;
            }
            $board->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::PLANET, true);

            return $board;
        }

        throw new PlanetXBoardGenerationException('Could not place dwarf planet', $board->toArray());
    }

    private function placeGalaxiesAndEmptySpaces(PlanetXBoard $board)
    {
        // if there is a single sector, one cloud must be placed in the single sector
        $emptyIndexes = $board->getSectorIndexesWithIcon();
        $emptySectorsBetweenTwo = $board->getSingleSectorIndexesWithoutIcon();

        // first option: place one single empty space and a cloud-empty-cloud constellation
        if (count($emptySectorsBetweenTwo) > 0) {
            $board->getSector($emptySectorsBetweenTwo[0])->setIconOrFail(PlanetXIconEnum::EMPTY_SPACE, true);

            foreach ($emptyIndexes as $emptySector) {
                $nextSector = ($emptySector + 1) % 12;
                $nextNextSector = ($emptySector + 2) % 12;

                if ($board->hasAnyIcon($nextSector) || $board->hasAnyIcon($nextNextSector)) {
                    continue;
                }

                $board->getSector($emptySector)->setIconOrFail(PlanetXIconEnum::GALAXY, true);
                $board->getSector($nextSector)->setIconOrFail(PlanetXIconEnum::EMPTY_SPACE, true);
                $board->getSector($nextNextSector)->setIconOrFail(PlanetXIconEnum::GALAXY, true);
            }

            return $board;
        }

        // second option: place two cloud-empty constellations
        $placedGalaxies = 0;
        foreach ($emptyIndexes as $emptySector) {
            $nextSector = ($emptySector + 1) % 12;
            if ($board->hasAnyIcon($nextSector) || $board->hasAnyIcon($emptySector)) {
                continue;
            }

            // checking for previous sector to avoid placing one galaxy in the middle of four empty sectors
            $previousSector = ($emptySector + 11) % 12;
            $previousPreviousSector = ($emptySector + 10) % 12;
            if (! $board->hasAnyIcon($previousSector) && $board->hasAnyIcon($previousPreviousSector)) {
                continue;
            }

            $placement = [$emptySector, $nextSector];
            shuffle($placement);
            $board->getSector($placement[0])->setIconOrFail(PlanetXIconEnum::GALAXY, true);
            $board->getSector($placement[1])->setIconOrFail(PlanetXIconEnum::EMPTY_SPACE, true);

            $placedGalaxies++;

            if ($placedGalaxies === 2) {
                return $board;
            }
        }

        throw new PlanetXBoardGenerationException('Could not place cloud-empty, cloud-empty constellation', $board->toArray());
    }
}
