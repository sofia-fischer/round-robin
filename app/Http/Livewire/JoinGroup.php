<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\User;
use App\Models\Group;
use App\Models\Player;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;

class JoinGroup extends Component
{
    public ?Game $game = null;

    public string $tab = 'register';

    public ?string $token = null;

    public ?string $errorMessage = null;

    public ?string $name = null;

    public ?string $email = null;

    public ?string $password = null;

    public int $step = 0;

    public string $stepTwo = 'register';

    public array $blobs = [
        [
            'M37.9,-52.5C45.6,-38.8,45.9,-23.5,51.7,-6.7C57.4,10,68.7,28.1,66.2,43.6C63.6,59,47.2,71.8,29.2,77C11.2,82.1,-8.4,79.5,-27.2,73.3C-46,67.1,-64,57.2,-65.7,43.3C-67.4,29.3,-52.8,11.3,-46.3,-5.1C-39.8,-21.6,-41.4,-36.4,-35,-50.4C-28.7,-64.4,-14.3,-77.6,0.4,-78.1C15.1,-78.5,30.3,-66.3,37.9,-52.5Z',
            'M41.6,-50.2C49,-43.4,46.6,-25.6,45,-11.3C43.5,2.9,42.8,13.6,38.5,23.2C34.1,32.7,26.1,41.1,14.6,49.2C3.2,57.3,-11.7,65.1,-23.6,62C-35.5,58.9,-44.5,44.9,-49.3,31.1C-54,17.4,-54.6,3.8,-52.2,-9.2C-49.9,-22.1,-44.6,-34.6,-35.5,-41.1C-26.3,-47.5,-13.1,-48.1,2,-50.5C17.1,-52.8,34.2,-57,41.6,-50.2Z',
            'M36.4,-44.3C49.1,-32.7,62.7,-23.1,69.7,-8.5C76.8,6,77.2,25.4,67.3,35.4C57.4,45.3,37.1,45.7,21.8,44.8C6.5,43.9,-3.9,41.5,-15.1,38.7C-26.3,35.8,-38.2,32.4,-46.3,24C-54.4,15.6,-58.5,2.3,-59,-13.1C-59.5,-28.4,-56.2,-45.8,-45.7,-57.8C-35.3,-69.9,-17.6,-76.7,-2.9,-73.2C11.8,-69.7,23.6,-56,36.4,-44.3Z',
        ],
        [
            'M46.8,-57.4C61.1,-43.7,73.5,-29.4,77.4,-12.8C81.3,3.8,76.7,22.7,65.9,35.1C55.2,47.5,38.4,53.5,24.1,52.8C9.9,52.1,-1.9,44.9,-18.8,42.6C-35.6,40.4,-57.6,43.1,-68.5,34.6C-79.4,26,-79.3,6,-76.7,-14.3C-74,-34.6,-68.8,-55.2,-55.6,-69.1C-42.4,-83.1,-21.2,-90.3,-2.5,-87.3C16.2,-84.4,32.4,-71.2,46.8,-57.4Z',
            'M32.8,-35.3C43.8,-29.9,54.9,-20.8,61.8,-7.1C68.8,6.6,71.6,24.8,64.6,37.6C57.5,50.3,40.6,57.7,23.4,63.7C6.2,69.8,-11.3,74.5,-25.2,69.4C-39,64.3,-49.1,49.2,-55.4,34.1C-61.6,18.9,-64,3.6,-58.4,-7.2C-52.8,-18,-39.2,-24.2,-28.1,-29.6C-17.1,-35,-8.5,-39.5,1.2,-40.9C10.9,-42.4,21.9,-40.7,32.8,-35.3Z',
            'M37.9,-46C51.7,-33.5,67.4,-24.1,71.9,-10.9C76.3,2.3,69.4,19.2,60.8,36.1C52.2,53,41.9,69.9,27.2,75.8C12.4,81.7,-6.8,76.6,-23,68.4C-39.3,60.3,-52.5,49.1,-54.8,36.1C-57.2,23,-48.5,8,-46,-8.5C-43.5,-24.9,-47.2,-42.7,-40.5,-56.5C-33.9,-70.3,-16.9,-80,-2.5,-77C12,-74.1,24,-58.5,37.9,-46Z',
        ],
        [
            'M43.3,-46.2C57.8,-39.5,72.4,-27.4,76.7,-12.1C81.1,3.2,75.4,21.7,65.8,37.8C56.3,53.9,42.9,67.6,28.4,68.9C13.9,70.3,-1.9,59.3,-20.5,53.7C-39.2,48,-60.7,47.8,-67.2,38.1C-73.6,28.4,-64.9,9.2,-60.7,-10C-56.4,-29.2,-56.6,-48.4,-47.2,-56C-37.9,-63.6,-18.9,-59.5,-2.3,-56.8C14.4,-54.1,28.8,-52.8,43.3,-46.2Z',
            'M41.4,-50.4C54,-38.8,64.7,-26.1,66.6,-12.2C68.5,1.6,61.5,16.6,54.3,33.5C47.1,50.4,39.6,69.4,26.3,75.7C13,81.9,-6.1,75.6,-18.7,65C-31.3,54.5,-37.5,39.8,-48.2,25.7C-58.9,11.5,-74.1,-2.1,-75.3,-16.4C-76.5,-30.7,-63.6,-45.6,-48.7,-56.7C-33.8,-67.9,-16.9,-75.3,-1.3,-73.8C14.4,-72.3,28.8,-61.9,41.4,-50.4Z',
            'M28.7,-36.4C35,-29,36.2,-17.8,37.4,-7.2C38.6,3.5,39.7,13.5,37.6,25.8C35.4,38.1,29.9,52.6,17.6,63.8C5.2,75,-13.9,83,-32.4,80.1C-50.9,77.3,-68.6,63.6,-69.7,47.2C-70.9,30.9,-55.4,11.9,-46.3,-2.7C-37.2,-17.3,-34.5,-27.5,-27.8,-34.9C-21.1,-42.3,-10.6,-46.8,0.3,-47.2C11.2,-47.6,22.5,-43.9,28.7,-36.4Z',
        ],
    ];

    private $names = [
        'Alpha',
        'Beta',
        'Gamma',
        'Delta',
        'Epsilon',
        'Digamma',
        'Zeta',
        'Eta',
        'Theta',
        'Iota',
        'Kappa',
        'Lambda',
        'Mu',
        'Nu',
        'Xi',
        'Omicron',
        'Pi',
        'Rho',
        'Sigma',
        'Tau',
        'Upsilon',
        'Phi',
        'Chi',
        'Psi',
        'Omega',
    ];

    public function render()
    {
        if (request()->get('game')) {
            $this->game = Game::where('uuid', request()->get('game'))->first();
            $this->step = 2;
        }

        return view('livewire.join-group');
    }

    public function checkToken()
    {
        $group = Group::where('token', $this->token)->first();

        if (! $group) {
            $this->errorMessage = 'This is not the group you are looking for... ';

            return;
        }

        if (Auth::user()) {
            $this->redirectToGroupRoom($group);
        }

        $this->errorMessage = null;
        $this->step = 1;
    }

    public function continueWithoutToken()
    {
        $this->token = null;
        $this->step = 1;
    }

    public function checkRegister()
    {
        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'That is not a valid email address... ';

            return;
        }

        if (! $this->password) {
            $this->errorMessage = 'Empty password is not an option... ';

            return;
        }

        $user = User::registerNew($this->name ?? collect($this->names)->random(), $this->email, $this->password);

        if ($this->token) {
            return $this->redirectToGroupRoom();
        }

        return $this->redirect('/welcome');
    }

    public function checkLogin()
    {
        $user = User::login($this->email, $this->password);

        if (! $user) {
            $this->errorMessage = 'Oh, that did not work... Can you try again?';

            return;
        }

        if ($this->token) {
            return $this->redirectToGroupRoom();
        }

        return $this->redirect('/welcome');
    }

    public function checkAnonymousPlay()
    {
        User::anonymLogin($this->name ?? collect($this->names)->random());
        $this->redirectToGroupRoom();
    }

    public function redirectToGroupRoom(Group $group = null)
    {
        $group = $group ?? $this->game->group ?? Group::where('token', $this->token)->first();

        $existingPlayer = $group->players()
            ->whereNotNull('user_id')
            ->where('user_id', Auth::id())
            ->first();

        if ($existingPlayer) {
            return $this->game
                ? $this->redirect('/game/' . $this->game->uuid)
                : $this->redirect('/group/' . $group->uuid);
        }

        $player = Player::create([
            'uuid'     => Str::uuid(),
            'user_id'  => Auth::user()->id,
            'group_id' => $group->id,
            'name'     => Auth::user()->name,
        ]);

        event(new PlayerCreated($player->id));

        return $this->game
            ? $this->redirect('/game/' . $this->game->uuid)
            : $this->redirect('/group/' . $group->uuid);
    }
}
