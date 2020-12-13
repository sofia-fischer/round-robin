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

    public ?string $errorMessage = null;

    private $names = [
        'Marine',
        'Cyan',
        'Emerald',
        'Ivory',
        'Citron',
        'Fuchsia',
        'Ruby',
        'Indigo',
        'Sterling',
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

        if (Auth::id()) {
            $user = Auth::user();
        }

        if ($this->tab == 'login') {
            $user = User::login($this->email, $this->password);
        }

        if ($this->tab == 'register') {
            $user = User::register($this->name, $this->email, $this->password);
        }

        Player::create([
            'uuid'     => Str::uuid(),
            'user_id'  => $user->id ?? null,
            'group_id' => $group->id,
            'name'     => $user->id ?? $this->name ?? collect($this->names)->random(),
            'counter'  => 0,
        ]);

        return $this->redirect('/group/' . $group->uuid);
    }
}
