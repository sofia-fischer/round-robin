<div>
    <div class="border-2 rounded-md border-gray-600 max-w-md">

        <div class="bg-gray-600 p-4 text-white">
           {{ count($group->player) }} Players
        </div>
        <div class="p-4">
            @foreach($group->player as $player)
                <div>
                    {{ $player->name }}
                </div>
            @endforeach
        </div>

    </div>

</div>
