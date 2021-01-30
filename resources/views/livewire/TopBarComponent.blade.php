<nav x-data="{ open: false }" class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 ">
    <!-- Primary Navigation Menu -->

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
        <div class="flex justify-between h-16">
            <!-- Navigation Links -->
            <div class="p-4 text-white text-xl">
                <a href="{{ url('/welcome') }}">
                    Round Robin
                </a>
            </div>

        @if(Auth::id())
            <!-- Group Selection -->
                <div class="text-white text-xs z-20 overflow-hidden pt-4 flex">
                    @foreach(\App\Models\Group::whereHas('authenticatedPlayer')->get() as $group)
                        @if($activeGroupUuid == $group->uuid)
                            <div class="mx-4 border-b-2 border-white flex h-8">

                                <div class="font-semibold py-1">
                                    {{ $group->token }}
                                </div>

                                <a href="{{ url('/group/' . $group->uuid) }}">
                                    <button class="ml-4 mt-1 px-4 h-5 bg-white bg-opacity-25 rounded-full">
                                        Lounge
                                    </button>
                                </a>

                                <button class="ml-4 mt-1 px-4 h-5 bg-white bg-opacity-25 rounded-full"
                                    wire:click="$toggle('showSettings')">
                                    Settings
                                </button>
                            </div>
                        @else
                            <a href="{{ url('/group/' . $group->uuid) }}">
                                <button
                                    class="rounded-full w-24 h-8 bg-opacity-25 mx-4 hover:border-b-2 hover:border-white">
                                    {{ $group->token }}
                                </button>
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Settings Dropdown -->
                <div>
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="p-4 text-white">
                                {{ Auth::user()->name }}
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- New Game -->
                            <x-jet-dropdown-link wire:click="newGroup">
                                {{ __('New Group') }}
                            </x-jet-dropdown-link>

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

    <div class="relative -mt-12 w-full pt-8 sm:pt-0 ">
        <svg viewBox="0 0 1428 174" version="1.1" >
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g transform="translate(-2.000000, 44.000000)" fill="#151E2D" fill-rule="nonzero">
                    <path
                        d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
                        opacity="0.2"></path>
                    <path
                        d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                        opacity="0.3"></path>
                    <path
                        d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
                        id="Path-4" opacity="0.4"></path>
                </g>
                <g transform="translate(-4.000000, 76.000000)" fill="#151E2D" fill-rule="nonzero">
                    <path
                        d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z"></path>
                </g>
            </g>
        </svg>
    </div>

    @if($showSettings)
        <livewire:game-settings-component :groupUuid="$activeGroupUuid"></livewire:game-settings-component>
    @endif
</nav>
