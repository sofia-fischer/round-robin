<x-app-layout>
    <style>
        .input-error {
            font-size: 0.875rem;
            line-height: 1rem;
            --tw-text-opacity: 1;
            color: rgb(255, 164, 164);
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            d="M37.9,-52.5C45.6,-38.8,45.9,-23.5,51.7,-6.7C57.4,10,68.7,28.1,66.2,43.6C63.6,59,47.2,71.8,29.2,77C11.2,82.1,-8.4,79.5,-27.2,73.3C-46,67.1,-64,57.2,-65.7,43.3C-67.4,29.3,-52.8,11.3,-46.3,-5.1C-39.8,-21.6,-41.4,-36.4,-35,-50.4C-28.7,-64.4,-14.3,-77.6,0.4,-78.1C15.1,-78.5,30.3,-66.3,37.9,-52.5Z"
                            transform="translate(100 100)"/>
                        <path style="stroke: none; fill: url(#gradient2) #ff890c" opacity="1"
                            d="M41.6,-50.2C49,-43.4,46.6,-25.6,45,-11.3C43.5,2.9,42.8,13.6,38.5,23.2C34.1,32.7,26.1,41.1,14.6,49.2C3.2,57.3,-11.7,65.1,-23.6,62C-35.5,58.9,-44.5,44.9,-49.3,31.1C-54,17.4,-54.6,3.8,-52.2,-9.2C-49.9,-22.1,-44.6,-34.6,-35.5,-41.1C-26.3,-47.5,-13.1,-48.1,2,-50.5C17.1,-52.8,34.2,-57,41.6,-50.2Z"
                            transform="translate(100 100)"/>
                        <path style="stroke: none; fill: url(#gradient3) #ff890c" opacity="0.5"
                            d="M36.4,-44.3C49.1,-32.7,62.7,-23.1,69.7,-8.5C76.8,6,77.2,25.4,67.3,35.4C57.4,45.3,37.1,45.7,21.8,44.8C6.5,43.9,-3.9,41.5,-15.1,38.7C-26.3,35.8,-38.2,32.4,-46.3,24C-54.4,15.6,-58.5,2.3,-59,-13.1C-59.5,-28.4,-56.2,-45.8,-45.7,-57.8C-35.3,-69.9,-17.6,-76.7,-2.9,-73.2C11.8,-69.7,23.6,-56,36.4,-44.3Z"
                            transform="translate(100 100)"/>
                    </svg>

                    <div class="absolute top-10 sm:top-32 left-0 right-0 bottom-0  h-full flex flex-col items-center
                    text-center text-white">

                        <div class="flex text-lg mt-auto p-8 font-semibold max-w-xl flex-wrap justify-center">
                            <div>
                                <a class="m-4 {{ $view === 'login' ? '' : 'opacity-50' }}"
                                    href="{{ route('auth.show', ['view' => 'login', 'token' => $token]) }}">
                                    Have an account? </a>
                            </div>
                            <div>
                                <a class="m-4 {{ $view === 'register' ? '' : 'opacity-50' }}"
                                    href="{{ route('auth.show', ['view' => 'register', 'token' => $token]) }}">
                                    Want an account? </a>
                            </div>
                            <div>
                                <a class="m-4 {{ $view === 'anonymous' ? '' : 'opacity-50' }}"
                                    href="{{ route('auth.show', ['view' => 'anonymous', 'token' => $token]) }}">
                                    Play anonymous? </a>
                            </div>
                        </div>

                        @if($view == 'register')
                            <div class="sm:mt-6">
                                <form method="POST" action="{{ route('auth.register') }}">
                                    @csrf

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="token"
                                            class="border-b-2 border-white bg-transparent"
                                            name="token"
                                            value="{{ old('token') ?? $token }}"
                                            autofocus
                                        />
                                        <label for="register-token"> Game Token (optional)</label>
                                        @error('token')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="name"
                                            class="border-b-2 border-white bg-transparent"
                                            name="name"
                                            :value="old('name')"
                                            required
                                        />
                                        <label for="name"> Name</label>
                                        @error('name')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="email"
                                            class="border-b-2 border-white bg-transparent"
                                            name="email"
                                            type="email"
                                            :value="old('email')"
                                            required
                                        />
                                        <label for="email"> Email</label>
                                        @error('email')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="password"
                                            class="border-b-2 border-white bg-transparent"
                                            type="password"
                                            name="password"
                                            required
                                        />
                                        <label for="password"> Password</label>
                                        @error('password')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex items-center justify-end mt-8">
                                        <button type="submit"
                                            class="bg-purple-700 rounded-full hover:bg-pink-600 py-2 px-4"
                                        >
                                            Join Game
                                        </button>
                                    </div>

                                </form>
                            </div>
                        @elseif($view == 'login')
                            <div class="sm:mt-6">
                                <form method="POST" action="{{ route('auth.login') }}">
                                    @csrf

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="token"
                                            class="border-b-2 border-white bg-transparent"
                                            name="token"
                                            value="{{ old('token') ?? $token }}"
                                            autofocus
                                        />
                                        <label for="register-token"> Game Token (optional)</label>
                                        @error('token')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="email"
                                            class="border-b-2 border-white bg-transparent"
                                            name="email"
                                            type="email"
                                            :value="old('email')"
                                            required
                                        />
                                        <label for="email"> Email</label>
                                        @error('email')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="password"
                                            class="border-b-2 border-white bg-transparent"
                                            type="password"
                                            name="password"
                                            required
                                        />
                                        <label for="password"> Password</label>
                                        @error('password')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex items-center justify-end mt-8">
                                        <button type="submit"
                                            class="bg-purple-700 rounded-full hover:bg-pink-600 py-2 px-4"
                                        >
                                            Join Game
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="sm:mt-6">
                                <form method="POST" action="{{ route('auth.anonymous') }}">
                                    @csrf

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="token"
                                            class="border-b-2 border-white bg-transparent"
                                            name="token"
                                            value="{{ old('token') ?? $token }}"
                                            autofocus
                                            required
                                        />
                                        <label for="register-token"> Game Token</label>
                                        @error('token')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex flex-col text-left mb-2">
                                        <input id="name"
                                            class="border-b-2 border-white bg-transparent"
                                            name="name"
                                            :value="old('name')"
                                            required
                                        />
                                        <label for="name"> Name</label>
                                        @error('name')<p class="input-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex items-center justify-end mt-8">
                                        <button type="submit"
                                            class="bg-purple-700 rounded-full hover:bg-pink-600 py-2 px-4"
                                        >
                                            Join Game
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-black opacity-50 w-screen h-screen absolute invisible left-0 top-0" id="overlay"></div>
            </div>

        </div>
    </div>
</x-app-layout>
