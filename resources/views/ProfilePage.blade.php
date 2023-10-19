<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg p-4 m-4 flex justify-between">
            <p>
                Do you want to log out?
            </p>

            <form method="POST" action="{{ route('user.logout') }}">
                @csrf
                <button type="submit" class="text-white rounded-xl m-2 p-2 hover:bg-pink-800 bg-pink-600">
                    Log Out
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg p-4 m-4">
            <form method="POST" action="{{ route('user.update') }}">
                Do you want to change some of your data?

                <div class="col-span-6 sm:col-span-4 py-2 rounded">
                    <input id="name" class="mt-1 block w-full" value="{{\Illuminate\Support\Facades\Auth::user()->name}}"/>
                    <label class="text-gray-400 text-sm" for="name">Name (optional)</label>
                </div>

                <div class="col-span-6 sm:col-span-4 py-2 rounded">
                    <input id="password" type="password" class="mt-1 block w-full" placeholder="***">
                    <label class="text-gray-400 text-sm" for="password">New Password (optional)</label>
                </div>

                <button type="submit" class="text-white rounded-xl m-2 p-2 px-5 hover:bg-gray-800 bg-gray-600">
                    {{ __('Save') }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg p-4 m-4 flex justify-between">
            <p>
                Do you want to delete your account?
            </p>

            <form method="POST" action="{{ route('user.destroy') }}"
                onsubmit="return confirm('Are you sure you want to delete your account?');">
                @csrf
                <button type="submit" class="text-white rounded-xl p-2 hover:bg-red-800 bg-red-600">
                    🚨 Delete Account
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
