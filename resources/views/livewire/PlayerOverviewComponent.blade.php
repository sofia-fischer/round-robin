<?php
/* @var App\Models\Group $group */
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>
<div>
    {{--  Token  --}}
    <div class="p-6 bg-gray-700">
        <div class="text-white text-lg font-semibold mb-4 text-center flex justify-evenly">
            <div>
                Token: <label class="text-pink-500">{{ $group->token }}</label>
            </div>
            @if($game)
                <div class="font-semibold text-white flex-grow">
                    {{ $game->logic->name }}
                </div>
            @endif
            <button class="h-full p-1" wire:click="$toggle('showKickPlayerModal')">⚙️</button>
        </div>

        <div class="flex w-full text-center">
            @foreach($group->players as $player)
                <div class="flex flex-col m-4 relative
                {{ $player->id == ($game->currentRound->active_player_id ?? null) ? ' mb-4' : '' }}
                {{ 'text-' . $player->activeColor }}">
                    <div class="rounded-full h-16 w-16 font-bold relative
                    {{ $player->id == ($game->currentRound->active_player_id ?? null) ?  'bg-' . $player->passiveColor  : 'bg-' . $player->passiveColor  }}">
                        <div class="absolute top-1/2 left-1/2">
                            {{ $player->scoreInGame($game->id) }}
                        </div>
                    </div>
                    <div>
                        {{ $player->name }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <x-jet-dialog-modal wire:model="showKickPlayerModal">
        <x-slot name="title">
            <h2 class="text-pink-500 text-center ">
                Game Settings
            </h2>
        </x-slot>

        <x-slot name="content">

            <div>
                <h2 class="text-semibold text-pink-500">
                    Player Settings
                </h2>
                <div class="flex my-8 mx-4 flex-wrap justify-between">
                    <div class="flex my-8 mx-4 flex-grow">
                        <label class="self-center mr-4">Name</label>
                        <input wire:model.defer='playerName'
                               class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500 ">
                    </div>

                    <div class="flex my-8 mx-4 flex-grow">
                        <label for='color' class="self-center mr-4">Color</label>
                        <select id="color"
                                class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500"
                                wire:model="color">
                            <option value="orange">Orange</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="green">Green</option>
                            <option value="blue">Blue</option>
                            <option value="purple">Purple</option>
                            <option value="pink">Pink</option>
                            <option value="gray">Gray</option>
                        </select>
                    </div>
                </div>
            </div>

            @if( $group->host_user_id == ($group->authenticatedPlayer->id ?? false))
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
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('showKickPlayerModal', false)">
                {{ __('Close') }}
            </x-jet-secondary-button>
            <x-jet-button wire:click="saveSettings">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>


