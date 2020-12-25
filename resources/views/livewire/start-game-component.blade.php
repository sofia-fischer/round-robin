<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-wrap content-center justify-evenly">

    <div class="bg-white overflow-hidden shadow-xl m-4 flex-grow">
        <div class="p-4 bg-gray-700">
            <h2 class="text-white text-lg font-semibold mb-4 text-center">
                Your active Groups
            </h2>
        </div>

        <div class="flex flex-wrap">
            @foreach(\App\Models\Group::where('host_user_id', Auth::id())->get() as $group)
                <a href="{{ url('/group/' . $group->uuid) }}">
                    <button class="p-4 hover:bg-pink-100 text-pink-500 font-semibold">
                        {{ $group->token }}
                    </button>
                </a>
            @endforeach
            <button class="p-4 hover:bg-pink-100 text-pink-500 font-semibold"
            wire:click="newGroup">
                New Group
            </button>
        </div>
    </div>
</div>
