<?php

namespace App\Models;

use App\Services\PlanetXConferenceGenerationService;
use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXConferences;
use App\ValueObjects\PlanetXRules\PlanetXRule;
use Illuminate\Database\Eloquent\Builder;

class PlanetXGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'planet_x';

    static $title = 'Planet X';

    static $description = 'The sky is separated in 12 Sections.
        In each Section is exactly one object out of Planet X, Galaxy, Dwarf Planet, Moon, Asteroid or Empty Space.
        The Players take turns and gather Information or publishing Theories on the different Sections.
        The Game ends when one Person locates Planet X and the surrounding Sections correctly.
        In the end Players are rewarded by correctly published Sections. ';

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    public function getCurrentBoard(): PlanetXBoard
    {
        return PlanetXBoard::fromArray($this->currentPayloadAttribute('board'));
    }

    public function getCurrentConference(): PlanetXConferences
    {
        return PlanetXConferences::fromArray($this->currentPayloadAttribute('conference'));
    }

    public function getCurrentNightSkyIndex(): int
    {
        return 0;
    }

    public function getAuthenticatedPlayerBoard(): PlanetXBoard
    {
        $move = $this->authenticatedCurrentMove;

        if ($move === null) {
            $move = $this->authenticatedPlayer->moves()->create([
                'round_id' => $this->currentRound->id,
                'user_id' => $this->authenticatedPlayer->user_id,
                'payload' => [
                    'board' => PlanetXBoard::playerBoard()->toArray(),
                ],
            ]);
        }

        return PlanetXBoard::fromArray($move->getPayloadWithKey('board'));
    }

    public function getAuthenticatedPlayerRules(): array
    {
        $storedRules = $this->authenticatedPlayer->payload['rules'] ?? null;

        if ($storedRules) {
            $hydratedRules = [];
            foreach ($storedRules as $key => $rule) {
                $hydratedRules[$key] = PlanetXRule::fromArray($rule);
            }

            return $hydratedRules;
        }

        $service = new PlanetXConferenceGenerationService();
        $rules = $service->generateRulesForBoard($this->getCurrentBoard(), 6);
        $payload = $this->authenticatedPlayer->payload;
        foreach ($rules as $key => $rule) {
            $payload['rules'][$key] = $rule->toArray();
        }
        $this->authenticatedPlayer->payload = $payload;
        $this->authenticatedPlayer->save();

        return $rules;
    }
}
