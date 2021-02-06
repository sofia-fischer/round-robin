<div>
    <div tabindex="0" class="z-40 overflow-auto left-0 top-0 bottom-0 right-0 w-full h-full fixed">
        <div class="z-50 relative p-3 mx-auto my-0 max-w-full" style="width: 600px;">
            <div class="bg-white rounded shadow-lg border flex flex-col overflow-hidden">
                <div class="px-6 py-3 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 text-white text-lg">Game
                    Settings
                </div>
                <div class="p-6 flex-grow">
                    <h2 class="text-semibold text-pink-500 pb-4">
                        Player Settings
                    </h2>
                    <div class="flex justify-between">
                        <div>
                            <input wire:model.defer='playerName'
                                   class="flex-grow border-b-2 border-gray-900 max-w-lg text-center">
                        </div>

                        <div class="flex flex-wrap max-w-md">
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

                <div class="px-6 py-3 flex justify-center">
                    @if(($group->authenticatedPlayer->id ?? false) != $kickPlayerId)
                        <button wire:click="$set('kickPlayerId', {{ $group->authenticatedPlayer->id ?? null}})"
                                class="bg-gradient-to-r from-orange-500 to-yellow-400 text-white text-md text-gray-200 rounded-full px-4 py-1">
                            Leave
                        </button>
                    @else
                        <button
                            wire:click="kickPlayer"
                            class="bg-gradient-to-r from-orange-500 to-red-500 text-white text-md text-gray-200 rounded-full px-4 py-1">
                            Click again if you sure you want to go
                        </button>
                    @endif
                </div>

                <?php
                /* @var App\Models\Group $group */
                /* @var App\Models\Player $player */
                ?>

                @if($isAdmin)
                    <div class="px-6 py-3 border-t">
                        <div>
                            <h2 class="text-semibold text-pink-500">
                                Kick a Player
                            </h2>
                            <div class="flex my-8 mx-4 flex-wrap text-center">
                                @foreach($group->players as $player)
                                    @if($player->user_id == $group->host_user_id)
                                    @elseif($player->id != $kickPlayerId)
                                        <button wire:click="$set('kickPlayerId', {{ $player->id }})"
                                                class="rounded-full font-bold m-2 px-2 text-white {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                                            {{ $player->name }}
                                        </button>
                                    @else
                                        <button
                                            wire:click="kickPlayer"
                                            class="rounded-full font-bold m-2 px-2 text-white bg-red-700">
                                            Click again to kick {{ $player->name }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="px-6 py-3 border-t flex justify-center">
                    <button type="button"
                            wire:click="saveSettings"
                            class="bg-gradient-to-r from-green-300 to-green-500 text-white text-md text-gray-200 rounded-full px-4 py-1">
                        Save
                    </button>
                </div>
            </div>
        </div>
        <div class="z-40 overflow-auto left-0 top-0 bottom-0 right-0 w-full h-full fixed bg-black opacity-75"
             wire:click="$emit('game-settings-close')"></div>
    </div>
</div>
