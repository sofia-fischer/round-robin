<div class="shadow-xl max-w-2xl mx-auto ">
    <div class="relative">

        <svg viewBox="0 0 200 200" class="absolute top-0 left-0 ">
            <defs>
                <linearGradient id="gradient1" x2="1" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
                <linearGradient id="gradient2" x2="1" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
                <linearGradient id="gradient3" x2="0.50" y2="1">
                    <stop offset="0%" stop-color="#5800E5"/>
                    <stop offset="100%" stop-color="#FF3300"/>
                </linearGradient>
            </defs>
            <path class="gradient-bg" style="stroke: none; fill: url(#gradient1) #ff3300" opacity="0.5"
                  d="{{ $blobs[$step][0] }}"
                  transform="translate(100 100)"/>
            <path style="stroke: none; fill: url(#gradient2) #ff890c" opacity="1"
                  d="{{ $blobs[$step][1] }}"
                  transform="translate(100 100)"/>
            <path style="stroke: none; fill: url(#gradient3) #ff890c" opacity="0.5"
                  d="{{ $blobs[$step][2] }}"
                  transform="translate(100 100)"/>
        </svg>

        <div class="absolute top-16 sm:top-44 left-0 right-0 bottom-0  h-full flex flex-col items-center
                    text-center text-white">

            @if($step == 0)
                <div class="text-lg mt-auto p-8 font-semibold">Do you have a Group Token?</div>
                <div class="sm:mt-6">
                    <input wire:keydown.enter="checkToken" wire:model.lazy='token'
                           class="border-b-2 border-white bg-transparent">
                    <button class="hover:text-yellow-300 h-5 w-5" wire:click="checkToken">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="text-sm">
                        {{ $errorMessage }}
                    </div>
                </div>
                @if(!Auth::id())
                    <button class="hover:text-yellow-300 text-sm mt-16 sm:mt-24" wire:click="continueWithoutToken">
                        Continue without token
                    </button>
                @endif
            @elseif($step == 1)
                <div class="text-lg mt-auto p-8 font-semibold">
                    Accept Cookies and Cake
                </div>
                <div class="sm:mt-6 text-xs max-w-sm">
                    We require JavaScript.
                    We require cookies to keep you logged in.
                    We require a third party websocket to communicate the game states between different players.
                    Outside of your Game, your User Data is only used for debugging.
                    <br>
                    The cake is lie.
                    <br>
                    <br>
                    Read more: COMING SOON!!!
                </div>
                <button class="hover:text-yellow-300 text-lg mt-12 sm:mt-20" wire:click="$set('step', 2)">
                    Accept
                </button>

            @elseif($step == 2 && $stepTwo == 'login')
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    @if($token)
                        <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'anonym')">
                            Play Anonymous?
                        </button>
                    @endif
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5"
                            wire:click="$set('stepTwo', 'register')">
                        Want an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Email</label>
                        <input wire:model.lazy='email' type="email"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Password</label>
                        <input wire:model.lazy='password' type="password"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkLogin">
                    Login
                </button>
            @elseif($step == 2 && $stepTwo == 'anonym')
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'register')">
                        Want an Account?
                    </button>
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5" wire:click="$set('stepTwo', 'login')">
                        Have an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Name</label>
                        <input wire:model.lazy='name' class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkAnonymousPlay">
                    Play
                </button>
            @else
                <div class="text-lg mt-auto p-4 font-semibold">Who are you?</div>
                <div>
                    @if($token)
                        <button class="hover:text-yellow-300 text-xs sm:mt-5" wire:click="$set('stepTwo', 'anonym')">
                            Play Anonymous?
                        </button>
                    @endif
                    <button class="hover:text-yellow-300 ml-4 text-xs sm:mt-5"
                            wire:click="$set('stepTwo', 'login')">
                        Have an Account?
                    </button>
                </div>

                <div class="sm:mt-6 text-xs max-w-sm">
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Name</label>
                        <input wire:model.lazy='name' class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Email</label>
                        <input wire:model.lazy='email' type="email"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                    <div class="flex my-8 mx-4">
                        <label class="self-center mr-4">Password</label>
                        <input wire:model.lazy='password' type="password"
                               class="flex-grow border-b-2 border-white bg-transparent">
                    </div>
                </div>
                <div class="text-xs">
                    {{ $errorMessage }}
                </div>
                <button class="hover:text-yellow-300 text-lg mt-2 sm:mt-5" wire:click="checkRegister">
                    Register
                </button>
            @endif
        </div>
    </div>
</div>
