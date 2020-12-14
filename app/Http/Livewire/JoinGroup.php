<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class JoinGroup extends Component
{
    public string $tab = 'login';

    public ?string $token = null;

    public ?string $name = null;

    public ?string $email = null;

    public ?string $password = null;

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
        return view('livewire.join-group');
    }

    public function joinGame()
    {
        $group = Group::where('token', $this->token)->firstOrFail();

        $existingPlayer = $group->player()
            ->whereNotNull('user_id')
            ->where('user_id', Auth::id())
            ->first();

        if ($existingPlayer) {
            return $this->redirect('/group/' . $group->uuid);
        }

        switch ($this->tab) {
            case 'login':
                $user = User::login($this->email, $this->password);
                break;
            case 'register':
                $user = User::registerNew($this->name, $this->email, $this->password);
                break;
            default:
                $user = User::anonymLogin($this->name ?? collect($this->names)->random());
                break;
        }

        Player::create([
            'uuid'     => Str::uuid(),
            'user_id'  => $user->id,
            'group_id' => $group->id,
            'name'     => $user->name,
            'counter'  => 0,
        ]);

        return $this->redirect('/group/' . $group->uuid);
    }
}
