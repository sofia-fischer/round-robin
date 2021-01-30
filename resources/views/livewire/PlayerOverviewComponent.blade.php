<?php
/* @var App\Models\Group $group */
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>
<div>
    {{--  Token  --}}
    <div class="p-6 bg-gray-900">
        <div class="flex w-full text-center px-4">
            @foreach($group->players as $index => $player)
                <div class="flex flex-col justify-end">
                    <div class="rounded-xl mx-4 overflow-hidden {{ 'bg-' . $player->activeColor }}">
                        <div class="p-1 text-white px-4">
                            {{ $player->name }}
                        </div>
                        @if($game)
                            <svg viewBox="0 0 1440 320" class="opacity-50 ">
                                <path fill="#FFFF" fill-opacity="1"
                                      d="{{ [
                                    'M0,224L80,240C160,256,320,288,480,298.7C640,309,800,299,960,261.3C1120,224,1280,160,1360,128L1440,96L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z',
                                    'M0,160L60,181.3C120,203,240,245,360,256C480,267,600,245,720,197.3C840,149,960,75,1080,74.7C1200,75,1320,149,1380,186.7L1440,224L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z',
                                    'M0,224L80,197.3C160,171,320,117,480,80C640,43,800,21,960,16C1120,11,1280,21,1360,26.7L1440,32L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z',
                                    'M0,32L80,80C160,128,320,224,480,272C640,320,800,320,960,277.3C1120,235,1280,149,1360,106.7L1440,64L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z'
                                ][$index % 4] }}">

                                </path>
                            </svg>
                            <div class="bg-white opacity-50 p-1">
                                {{ $game ? $player->scoreInGame($game->id) : '' }}
                            </div>
                        @endif
                    </div>

                    @if($game && $player->id == ($game->currentRound->active_player_id ?? null))
                        <div class="w-2 h-4"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>


