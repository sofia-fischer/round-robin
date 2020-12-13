<div class="w-full max-w-2xl mx-auto">
    <div class="flex my-8 mx-4 w-full max-w-lg mx-auto">
        <label class="self-center mr-4">Game Token:</label>
        <input wire:model.defer='token' class=" border-b-2 border-orange-500 flex-grow">
    </div>

    @if(!Auth::id())
        <div class="flex text-center">
            <div
                class="p-2 flex-grow hover:bg-orange-200 bg-orange-{{ $tab == 'join' ? '300 text-orange-600 font-semibold' : '100 text-orange-400' }}"
                wire:click="$set('tab', 'join')">
                Just Play
            </div>
            <div
                class="p-2 flex-grow hover:bg-orange-200 bg-orange-{{ $tab == 'login' ? '300 text-orange-600 font-semibold' : '100 text-orange-400' }}"
                wire:click="$set('tab', 'login')">
                Login
            </div>
            <div
                class="p-2 flex-grow hover:bg-orange-200 bg-orange-{{ $tab == 'register' ? '300 text-orange-600 font-semibold' : '100 text-orange-400' }}"
                wire:click="$set('tab', 'register')">
                Register
            </div>
        </div>

        <div class="w-full max-w-lg mx-auto">
            @if($tab == 'register')
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Name</label>
                    <input wire:model.defer='name' class="flex-grow border-b-2 border-orange-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Email</label>
                    <input wire:model.defer='email' type="email" class="flex-grow border-b-2 border-orange-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Password</label>
                    <input wire:model.defer='password' type="password" class="flex-grow border-b-2 border-orange-500 ">
                </div>
            @elseif($tab == 'login')
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Email</label>
                    <input wire:model.defer='email' type="email" class="flex-grow border-b-2 border-orange-500 ">
                </div>
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Password</label>
                    <input wire:model.defer='password' type="password" class="flex-grow border-b-2 border-orange-500 ">
                </div>
            @else()
                <div class="flex my-8 mx-4">
                    <label class="self-center mr-4">Name</label>
                    <input wire:model.defer='name' class="flex-grow border-b-2 border-orange-500 ">
                </div>
            @endif
        </div>
    @endif

    <div class="flex content-center">
        <button wire:click="joinGame"
                class="mx-auto text-white rounded-full m-4 px-4 py-1 bg-orange-{{ $token && (Auth::id() || $name || ($email && $password)) ? '500' : '200' }}">
            Play
        </button>
    </div>
</div>
