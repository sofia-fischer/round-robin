<div class="flex flex-wrap">
    @foreach($games as $game)
        <button class="p-4 hover:bg-orange-100 text-orange-500 font-semibold"
                wire:click="startGame({{ $game->id }})">
            {{ $game->name }}
        </button>
    @endforeach
</div>
