<div>
    <div tabindex="0" class="z-40 overflow-auto left-0 top-0 bottom-0 right-0 w-full h-full fixed">
        <div class="z-50 relative p-3 mx-auto my-0 max-w-full" style="width: 600px;">
            <div class="bg-white rounded shadow-lg border flex flex-col overflow-hidden">
                <div class="px-6 py-3 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 text-white text-lg">Game Settings</div>
                <div class="p-6 flex-grow">
                    <h2 class="text-semibold text-pink-500">
                        Player Settings
                    </h2>
                    <div class="flex my-8 mx-4 justify-between">
                        <div class="flex my-8 mx-4 flex-grow">
                            <label class="self-center mr-4">Name</label>
                            <input wire:model.defer='playerName' class="flex-grow border-b-2 border-pink-500">
                        </div>

                        <div class="flex my-8 mx-4 flex-grow flex-wrap max-w-md">
                            <button class="rounded-full m-1 w-4 h-4 bg-orange-500
                                {{ $color == 'orange' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'orange')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-red-500
                                {{ $color == 'red' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'red')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-yellow-500
                                {{ $color == 'yellow' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'yellow')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-green-500
                                {{ $color == 'green' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'green')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-blue-500
                                {{ $color == 'blue' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'blue')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-purple-500
                                {{ $color == 'purple' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'purple')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-pink-500
                                {{ $color == 'pink' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'pink')"></button>
                            <button class="rounded-full m-1 w-4 h-4 bg-gray-500
                                {{ $color == 'gray' ? '' : 'opacity-50' }}"
                                    wire:click="$set('color', 'gray')"></button>
                        </div>
                    </div>
                </div>

                @if($isAdmin)
                    <div class="px-6 py-3 border-t">
                        <div>
                            <h2 class="text-semibold text-pink-500">
                                Kick a Player
                            </h2>
                            <div class="flex my-8 mx-4 flex-wrap">
                                @foreach($group->players as $player)
                                    @if($player->id != $kickPlayerId)
                                        <button wire:click="$set('kickPlayerId', {{ $player->id }})"
                                                class="rounded-full h-16 w-16 font-bold m-2 text-white {{ 'bg-' . $player->activeColor }}">
                                            <div class="text-center pt-1">{{ $player->name }}</div>
                                        </button>
                                    @else
                                        <button
                                            wire:click="kickPlayer"
                                            class="rounded-full h-16 font-bold m-2 text-white bg-red-700">
                                            <div class="text-center px-4">Click again to kick {{ $player->name }}</div>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="px-6 py-3 border-t flex">
                    <button type="button"
                            wire:click="saveSettings"
                            class="bg-blue-600 text-gray-200 rounded px-4 py-2 mx-8 flex-grow">
                        Save
                    </button>
                </div>
            </div>
        </div>
        <div class="z-40 overflow-auto left-0 top-0 bottom-0 right-0 w-full h-full fixed bg-black opacity-75"
             wire:click="$emit('game-settings-close')"></div>
    </div>
</div>
