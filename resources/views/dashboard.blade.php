<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-4">
                    <h2 class="text-orange-500 text-lg font-semibold mb-4">
                        Welcome to Group Robin
                    </h2>

                    <p>
                        This is a Multiplayer Plattform for round based games.
                    </p>
                </div>

                <div>
                    <livewire:start-game-component></livewire:start-game-component>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
