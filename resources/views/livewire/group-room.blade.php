<div class="flex flex-wrap justify-evenly">
    <div class="border-2 rounded-md border-gray-600 flex-grow m-4">
        <div class="bg-gray-600 p-4 text-white">
            In the Room
        </div>
        <div>
            @foreach($group->player as $player)
                <div class="p-4 bg-{{ $player->color ?? 'white' }}-100 flex justify-between">
                    {{ $player->name }}

                    @if($group->host_user_id == Auth::id() && $player->id != Auth::id())
                        <button wire:click="kickPlayer({{$player->id}})" wire:loading.attr="disabled" class="text-md">
                            ❌
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="border-2 rounded-md border-gray-600 flex-grow m-4">
        <div class="bg-gray-600 p-4 text-white">
            Player Settings
        </div>
        <div class="p-4">
            <div class="flex my-8 mx-4">
                <label class="self-center mr-4">Name</label>
                <input wire:model.defer='playerName'
                       class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500 "
                       wire:keydown.enter="updatePlayerName">
            </div>

            <div class="flex my-8 mx-4">
                <label for='color' class="self-center mr-4">Color</label>
                <select id="color" class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500"
                        wire:change="updateColor"
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

    @if($group->host_user_id == Auth::id())
        <div class="border-2 rounded-md border-gray-600 flex-grow m-4">
            <div class="bg-gray-600 p-4 text-white">
                Game Settings
            </div>
            <div class="p-4 flex flex-col content-center">
                <div class="flex my-8 mx-4">
                    <label for='color' class="self-center mr-4">Game</label>
                    <select id="color" class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500"
                            wire:change="updateGame"
                            wire:model="game">
                        <option value="1">Wavelength</option>
                    </select>
                </div>

                <button class="bg-{{ $group->authenticatedPlayer->color }}-500 text-white rounded-full px-4 py-2"
                        wire:click="startGame">
                    Start
                </button>
            </div>
        </div>
    @endif

        <script>
            (function () {
                document.addEventListener('livewire:load', () => {
                    console.log('alive')

                    Echo.channel('lol')
                        .notification(event => console.log(event))
                        .on('App\\Queue\\Events\\PlayerUpdated', event => console.log(event))
                });
            }());
        </script>
</div>
