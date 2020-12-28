<nav x-data="{ open: false }" class="bg-white bg-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="flex text-white flex-grow abolute w-screen left-0 overflow-hidden z-10 ">

        <?php
        $possibleSize = [0, 2, 4, 8, 10, 12, 16, 20, 24, 32];
        $possibleColors = ['orange', 'red', 'yellow', 'green', 'blue', 'purple', 'pink', 'gray'];
        ?>

        <div class="absolute overflow-hidden flex  h-16">
            @foreach(array_fill(0, 5, null) as $bubble)
                <div
                    class="relative bg-{{$possibleColors[random_int(0,7)]}}-400 rounded-full w-16 h-16 bg-opacity-25
                        ml-{{ array_rand($possibleSize) * 4 }}
                        mr-{{ array_rand($possibleSize) * 4 }}
                    {{ !!random_int(0,1) ? 'top' : 'bottom' }}-{{ array_rand($possibleSize) }}
                        right-{{ array_rand($possibleSize) }}
                        "></div>
            @endforeach
        </div>

        <div class="absolute overflow-hidden flex  h-16">
            @foreach(array_fill(0, 5, null) as $bubble)
                <div
                    class="relative bg-{{$possibleColors[random_int(0,7)]}}-400 rounded-full w-12 h-12 bg-opacity-25
                        ml-{{ array_rand($possibleSize) * 4 }}
                        mr-{{ array_rand($possibleSize) * 4 }}
                    {{ !!random_int(0,1) ? 'top' : 'bottom' }}-{{ array_rand($possibleSize) }}
                        right-{{ array_rand($possibleSize) }}
                        "></div>
            @endforeach
        </div>

        <div class="absolute overflow-hidden flex  h-16">
            @foreach(array_fill(0, 5, null) as $bubble)
                <div
                    class="relative bg-{{$possibleColors[random_int(0,7)]}}-400 rounded-full w-8 h-8 bg-opacity-25
                        ml-{{ array_rand($possibleSize) * 4 }}
                        mr-{{ array_rand($possibleSize) * 4 }}
                    {{ !!random_int(0,1) ? 'top' : 'bottom' }}-{{ array_rand($possibleSize) }}
                        right-{{ array_rand($possibleSize) }}
                        "></div>
            @endforeach
        </div>

        <div class="absolute overflow-hidden flex  h-16">
            @foreach(array_fill(0, 5, null) as $bubble)
                <div
                    class="relative bg-{{$possibleColors[random_int(0,7)]}}-400 rounded-full w-4 h-4 bg-opacity-25
                        ml-{{ array_rand($possibleSize) * 4 }}
                        mr-{{ array_rand($possibleSize) * 4 }}
                    {{ !!random_int(0,1) ? 'top' : 'bottom' }}-{{ array_rand($possibleSize) }}
                        right-{{ array_rand($possibleSize) }}
                        "></div>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 text-pink-400 sm:-my-px sm:ml-10 sm:flex pt-4 text-lg z-20">
                    <a href="{{ url('/welcome') }}">
                        Round Robin
                    </a>
                </div>
            </div>

        @if(Auth::id())

            <!-- Group Selection -->
                <div class="text-white text-xs z-20 overflow-hidden">
                    <button class="bg-green-400 rounded-full w-16 h-16 bg-opacity-25 hover:bg-opacity-100 mx-4 p-2"
                            wire:click="newGroup">
                        New Round
                    </button>

                    @foreach(\App\Models\Group::whereHas('players', function ($playerQuery){ $playerQuery->where('user_id', Auth::id());})->get() as $group)
                        <a href="{{ url('/group/' . $group->uuid) }}">
                            <button
                                class="rounded-full w-16 h-16 bg-opacity-25 mx-4 hover:bg-opacity-100  bg-{{$possibleColors[random_int(0,7)]}}-400 ">
                                {{ $group->token }}
                            </button>
                        </a>
                    @endforeach
                </div>


                <!-- Settings Dropdown -->
                <div>
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="p-4 text-pink-400">
                                {{ Auth::user()->name }}
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-jet-dropdown-link>

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                                     onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                    {{ __('Logout') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            @endif
        </div>
    </div>

</nav>
