<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 bg-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-jet-application-mark class="block h-9 w-auto"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 text-orange-500 sm:-my-px sm:ml-10 sm:flex pt-4 text-lg">
                    <a href="{{ route('dashboard') }}">
                        {{ __('Round Robin') }}
                    </a>
                </div>
            </div>

        @if(Auth::id())
            <!-- Settings Dropdown -->
                <div>
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="p-4 text-orange-500">
                                {{ Auth::user()->name }}
                            </button>
                        </x-slot>

                        <x-slot name="content" >
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
