<?php
/* @var App\Models\Group $group */
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>
<div class="absolute sm:relative sm:ml-4 md:ml-10">
    <div class="flex flex-col w-full text-center px-4 invisible pt-16 sm:visible">
        @foreach($group->players as $index => $player)
            <div class="flex flex-row justify-start mb-2  {{ $game && $player->id == ($game->currentRound->active_player_id ?? null) ? 'pl-4' : '' }}">
                <div class="flex overflow-hidden justify-between rounded-xl w-7 h-7 sm:w-36
                        {{ 'bg-' . $player->activeColor }}">
                    <div class="pt-1 text-white px-2 ">
                        {{ $player->name }}
                    </div>
                    @if($game)
                        <div class="text-sm bg-white opacity-50 m-1 rounded-full px-2">
                            {{ $game ? $player->scoreInGame($game->id) : '' }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    {{--  Mobiel view  --}}
    <div class="flex flex-col w-full text-center px-4 absolute sm:invisible" style="left: -2rem">
        @foreach($group->players as $index => $player)
            <div class="flex flex-row justify-start mb-2  {{ $game && $player->id == ($game->currentRound->active_player_id ?? null) ? 'pl-4' : '' }}">
                <div class="flex overflow-hidden justify-between rounded-full w-7 h-7 sm:w-36
                     {{ 'bg-' . $player->activeColor }}">
                </div>
            </div>
        @endforeach
    </div>
</div>


