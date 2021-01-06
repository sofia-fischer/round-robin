<nav x-data="{ open: false }" class="bg-white bg-gray-800">
    <!-- Primary Navigation Menu -->

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
                <div class="text-white text-xs z-20 overflow-hidden pt-4">
                    @foreach(\App\Models\Group::whereHas('players', function ($playerQuery){ $playerQuery->where('user_id', Auth::id());})->get() as $group)
                        <a href="{{ url('/group/' . $group->uuid) }}">
                            <button
                                class="rounded-full w-24 h-8 bg-opacity-25 mx-4 hover:bg-opacity-100 {{ Str::endsWith(Request::url(), '/group/' . $group->uuid) ? 'bg-pink-400 bg-opacity-50': 'bg-gray-400' }}">
                                {{ $group->token }}
                            </button>
                        </a>
                    @endforeach
                    <button class="rounded-full w-8 h-8 bg-opacity-25 mx-4 hover:bg-opacity-100  bg-green-400 "
                            wire:click="newGroup">
                        +
                    </button>
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
