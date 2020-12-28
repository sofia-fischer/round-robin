<div class="shadow-xl max-w-2xl mx-auto bg-white">
    {{--  Token  --}}
    <div class="p-4 bg-gray-700">
        <h2 class="text-white text-lg font-semibold mb-4 text-center flex justify-center">
            <label class="self-center mr-4">Group Token:</label>
            <input wire:model.defer='token' class="border-b-2 border-pink-500 bg-gray-700 ">
        </h2>
    </div>

    @if(!Auth::id())
        <div class="flex text-center">
            <div
                class="p-2 flex-grow {{ $tab == 'join' ? 'bg-pink-200 text-pink-500 font-semibold' : 'hover:bg-pink-200 text-pink-400 ' }}"
                wire:click="$set('tab', 'join')">
                Just Play
            </div>
            <div
                class="p-2 flex-grow {{ $tab == 'login' ? 'bg-pink-200 text-pink-500 font-semibold' : 'hover:bg-pink-200 text-pink-400 ' }}"
                wire:click="$set('tab', 'login')">
                Login
            </div>
            <div
                class="p-2 flex-grow {{ $tab == 'register' ? 'bg-pink-200 text-pink-500 font-semibold' : 'hover:bg-pink-200 text-pink-400 ' }}"
                wire:click="$set('tab', 'register')">
                Register
            </div>
        </div>

        <div class="w-full max-w-lg mx-auto bg-white">
            @if($tab == 'register')
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Name</label>
                    <input wire:model.defer='name' class="flex-grow border-b-2 border-pink-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Email</label>
                    <input wire:model.defer='email' type="email" class="flex-grow border-b-2 border-pink-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Password</label>
                    <input wire:model.defer='password' type="password" class="flex-grow border-b-2 border-pink-500 ">
                </div>
            @elseif($tab == 'login')
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Email</label>
                    <input wire:model.defer='email' type="email" class="flex-grow border-b-2 border-pink-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Password</label>
                    <input wire:model.defer='password' type="password" class="flex-grow border-b-2 border-pink-500 ">
                </div>
            @else()
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Name</label>
                    <input wire:model.defer='name' class="flex-grow border-b-2 border-pink-500 ">
                </div>
            @endif
        </div>
    @endif

    <div class="flex content-center">
        <button wire:click="joinGame"
                {{ (Auth::id() && $token) || ($name && $token) || ($email && $password) ? '' : 'disabled'}}
                class="mx-auto text-white rounded-full m-4 px-4 py-1 {{ (Auth::id() && $token) || ($name && $token) || ($email && $password) ? 'bg-pink-500' : 'bg-red-100'}}">
            Join the Game
        </button>
    </div>
</div>
