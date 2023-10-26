<?php

namespace Tests\Unit;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXRules\InABandOfNSectorsRule;
use App\ValueObjects\PlanetXRules\InSectorRule;
use App\ValueObjects\PlanetXRules\NextToRule;
use App\ValueObjects\PlanetXRules\NotInSectorRule;
use App\ValueObjects\PlanetXRules\NotNextToRule;
use App\ValueObjects\PlanetXRules\WithinNSectorsRule;
use App\ValueObjects\PlanetXSector;
use PHPUnit\Framework\TestCase;

class PlanetXRulesTest extends TestCase
{
    private PlanetXBoard $board;

    protected function setUp(): void
    {
        parent::setUp();

        $this->board = new PlanetXBoard(
            new PlanetXSector(planetX: true),
            new PlanetXSector(emptySpace: true),
            new PlanetXSector(galaxy: true),
            new PlanetXSector(moon: true),
            new PlanetXSector(planet: true),
            new PlanetXSector(galaxy: true),
            new PlanetXSector(emptySpace: true),
            new PlanetXSector(asteroid: true),
            new PlanetXSector(asteroid: true),
            new PlanetXSector(asteroid: true),
            new PlanetXSector(asteroid: true),
            new PlanetXSector(moon: true),
        );
    }

    public function testInSectorRule()
    {
        $validRule = new InSectorRule(PlanetXIconEnum::PLANET_X, 0);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new InSectorRule(PlanetXIconEnum::PLANET_X, 1);
        $this->assertFalse($invalidRule->isValid($this->board));
    }

    public function testNotInSectorRule()
    {
        $validRule = new NotInSectorRule(PlanetXIconEnum::PLANET_X, 1);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new NotInSectorRule(PlanetXIconEnum::PLANET_X, 0);
        $this->assertFalse($invalidRule->isValid($this->board));
    }

    public function testNextToRule()
    {
        $validRule = new NextToRule(PlanetXIconEnum::PLANET_X, PlanetXIconEnum::MOON);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new NextToRule(PlanetXIconEnum::PLANET_X, PlanetXIconEnum::EMPTY_SPACE);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new NextToRule(PlanetXIconEnum::PLANET_X, PlanetXIconEnum::GALAXY);
        $this->assertFalse($invalidRule->isValid($this->board));
    }

    public function testNotNextToRule()
    {
        $validRule = new NotNextToRule(PlanetXIconEnum::PLANET_X, PlanetXIconEnum::GALAXY);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new NotNextToRule(PlanetXIconEnum::ASTEROID, PlanetXIconEnum::ASTEROID);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new NotNextToRule(PlanetXIconEnum::PLANET_X, PlanetXIconEnum::MOON);
        $this->assertFalse($invalidRule->isValid($this->board));
    }

    public function testInABandOfNSectors()
    {
        $validRule = new InABandOfNSectorsRule(PlanetXIconEnum::ASTEROID, 4);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new InABandOfNSectorsRule(PlanetXIconEnum::MOON, 5);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new InABandOfNSectorsRule(PlanetXIconEnum::MOON, 6);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new InABandOfNSectorsRule(PlanetXIconEnum::EMPTY_SPACE, 6);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new InABandOfNSectorsRule(PlanetXIconEnum::GALAXY, 3);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new InABandOfNSectorsRule(PlanetXIconEnum::MOON, 4);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new InABandOfNSectorsRule(PlanetXIconEnum::EMPTY_SPACE, 5);
        $this->assertFalse($invalidRule->isValid($this->board));
    }

    public function testWithingNSectorsRule()
    {
        $validRule = new WithinNSectorsRule(PlanetXIconEnum::MOON, 3, PlanetXIconEnum::GALAXY);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new WithinNSectorsRule(PlanetXIconEnum::PLANET, 2, PlanetXIconEnum::EMPTY_SPACE);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new WithinNSectorsRule(PlanetXIconEnum::PLANET, 4, PlanetXIconEnum::PLANET_X);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new WithinNSectorsRule(PlanetXIconEnum::GALAXY, 4, PlanetXIconEnum::GALAXY);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new WithinNSectorsRule(PlanetXIconEnum::MOON, 2, PlanetXIconEnum::GALAXY);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new WithinNSectorsRule(PlanetXIconEnum::MOON, 3, PlanetXIconEnum::ASTEROID);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new WithinNSectorsRule(PlanetXIconEnum::PLANET_X, 1, PlanetXIconEnum::ASTEROID);
        $this->assertFalse($invalidRule->isValid($this->board));
    }


}
