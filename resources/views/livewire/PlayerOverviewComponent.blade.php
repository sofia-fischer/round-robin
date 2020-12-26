<?php
/* @var App\Models\Group $group */
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>
<div>
    <div class="rounded-full flex w-full overflow-hidden my-4">
        @foreach($group->players as $player)
            <div
                class="bg-{{ $player->id == ($game->currentRound->active_player_id ?? null) ? $player->activeColor : $player->passiveColor }} flex-grow p-4">
                <label class="text-{{ $player->activeColor }}">{{ $player->name }}</label>
                @if($game)
                    <label class="text-gray-400">( {{ $player->scoreInGame($game->id) }} )</label>
                @endif
            </div>
        @endforeach
        <div class="bg-gray-700">
            <button class="h-full p-1" wire:click="$toggle('showKickPlayerModal')">🔧</button>
        </div>
    </div>

    <x-jet-dialog-modal wire:model="showKickPlayerModal">
        <x-slot name="title">
            <h2 class="text-pink-500 text-center">
                Game Settings
            </h2>
        </x-slot>

        <x-slot name="content">

            <div>
                <h2 class="text-semibold text-pink-500">
                    Kick a Player
                </h2>
                <div class="flex my-8 mx-4 flex-wrap">
                    <select id="playerSelection" class="flex-grow border-b-2"
                            wire:change="$set('kickPlayerId', {{ $player->id }})"
                            wire:model="kickPlayerId">

                        @foreach($group->players as $player)
                            <option value="{{ $player->id }}"> {{ $player->name }}</option>
                        @endforeach
                    </select>
                    <button
                        class=" mx-2 rounded-full text-white px-4 py-1 bg-pink-{{ $surePlayerKick ? '200' : '500' }}"
                        wire:click="$toggle('surePlayerKick')">
                        Kick
                    </button>
                    <button
                        class=" mx-2 rounded-full text-white px-4 py-1 {{ $surePlayerKick ? 'bg-pink-500' : 'hidden' }}"
                        wire:click="kickPlayer({{$player->id}})">
                        I am sure, Kick!
                    </button>
                </div>
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('showKickPlayerModal', false)">
                {{ __('Close') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>


