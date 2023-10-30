<?php

namespace Tests\Unit;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXRules\InABandOfNSectorsRule;
use App\ValueObjects\PlanetXRules\InSectorRule;
use App\ValueObjects\PlanetXRules\NextToRule;
use App\ValueObjects\PlanetXRules\NotInSectorRule;
use App\ValueObjects\PlanetXRules\NotNextToRule;
use App\ValueObjects\PlanetXRules\NotWithinNSectorsRule;
use App\ValueObjects\PlanetXRules\WithinNSectorsRule;
use PHPUnit\Framework\TestCase;

class PlanetXRulesTest extends TestCase
{
    private PlanetXBoard $board;

    protected function setUp(): void
    {
        parent::setUp();

        $this->board = PlanetXBoard::fromArray([
            [PlanetXIconEnum::PLANET_X->value],
            [PlanetXIconEnum::EMPTY_SPACE->value],
            [PlanetXIconEnum::GALAXY->value],
            [PlanetXIconEnum::MOON->value],
            [PlanetXIconEnum::PLANET->value],
            [PlanetXIconEnum::GALAXY->value],
            [PlanetXIconEnum::EMPTY_SPACE->value],
            [PlanetXIconEnum::ASTEROID->value],
            [PlanetXIconEnum::ASTEROID->value],
            [PlanetXIconEnum::ASTEROID->value],
            [PlanetXIconEnum::ASTEROID->value],
            [PlanetXIconEnum::MOON->value],
        ]);
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

    public function testNotWithingNSectorsRule()
    {
        $validRule = new NotWithinNSectorsRule(PlanetXIconEnum::PLANET_X, 3, PlanetXIconEnum::PLANET);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new NotWithinNSectorsRule(PlanetXIconEnum::MOON, 3, PlanetXIconEnum::MOON);
        $this->assertTrue($validRule->isValid($this->board));

        $validRule = new NotWithinNSectorsRule(PlanetXIconEnum::PLANET_X, 2, PlanetXIconEnum::PLANET);
        $this->assertTrue($validRule->isValid($this->board));

        $invalidRule = new NotWithinNSectorsRule(PlanetXIconEnum::PLANET_X, 2, PlanetXIconEnum::MOON);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new NotWithinNSectorsRule(PlanetXIconEnum::EMPTY_SPACE, 3, PlanetXIconEnum::MOON);
        $this->assertFalse($invalidRule->isValid($this->board));

        $invalidRule = new NotWithinNSectorsRule(PlanetXIconEnum::ASTEROID, 1, PlanetXIconEnum::ASTEROID);
        $this->assertFalse($invalidRule->isValid($this->board));
    }


}
