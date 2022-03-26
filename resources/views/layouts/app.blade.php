<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link rel="icon" type="image/png" href="{!! asset('storage/images/round_robin_favicon.png') !!}"/>
</head>
<body class="font-sans antialiased bg-gray-900">
<div class="min-h-screen">

    <!-- Page Heading -->
    <header>
        <livewire:top-bar-component></livewire:top-bar-component>
    </header>
    <!-- Page Content -->

    <main>
        {{ $slot }}
    </main>

    <footer class="fixed bottom-0 text-gray-400 text-sm w-full text-center">
        <button id="footer-contact-trigger">
            Contact
        </button>
    </footer>
    <style>
        .is-visible {
            visibility: visible;
            pointer-events: auto;
        }
    </style>

    <div class="bg-black opacity-50 w-screen h-screen absolute invisible left-0 top-0" id="footer-contact-cover"></div>
    <div
        class="bg-white text-small invisible rounded-lg shadow-lg max-w-xl top-32 w-full left-0 right-0 mx-auto p-4 text-center absolute overflow-y-scroll"
        id="footer-contact-modal">
        <h2 class="text-center text-purple-600 pb-2">
            Contact
        </h2>

        <div>
            Sofia Fischer
            <br>
            sofia@philodev.one
            <br><br>
            This is a page for the private usage of me and my friends and not used for any kind of commercial interests.
        </div>

        <h2 class="text-center text-purple-600 pb-2 mt-6">
            Looking for a Developer?
        </h2>
        <div>
            I am a philomathic fullstack Developer always looking for the next challenge - feel free to contact me.
        </div>
    </div>

    <script>
        document.getElementById('footer-contact-trigger').addEventListener('click', function () {
            document.getElementById('footer-contact-cover').classList.add('is-visible');
            document.getElementById('footer-contact-modal').classList.add('is-visible');
        });

        document.getElementById('footer-contact-cover').addEventListener('click', function () {
            document.getElementById('footer-contact-cover').classList.remove('is-visible');
            document.getElementById('footer-contact-modal').classList.remove('is-visible');
        });

    </script>
</div>

@stack('modals')

@livewireScripts
@stack('scripts')
</body>
</html>
