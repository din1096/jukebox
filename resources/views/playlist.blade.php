<x-layout>
    <x-slot:heading>
        Je Playlist
    </x-slot:heading>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($songs->count())
        <ul>
            @foreach($songs as $song)
                <li class="mb-2">
                    <a href="/songs/{{ $song->id }}" class="text-blue-500 hover:underline">
                        {{ $song->name }}
                    </a>
                    <span class="text-gray-600 ml-2">
                        ({{ \Carbon\CarbonInterval::seconds($song->duration)->cascade()->forHumans(['short' => true]) }})
                    </span>  

                    <form action="{{ route('playlist.remove', $song->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:underline ml-2">Verwijder</button>
                    </form>
                </li>
            @endforeach
        </ul>
        <form method="GET" action="{{ route('playlist.index') }}" class="mt-4">
            <button type="submit" name="show_total" value="1" class="text-red-500 hover:underline">
            Total Time
            </button>
        </form>

        @if(request('show_total'))
            <p class="mt-2 font-semibold">
                {{ $totalTime }}
            </p>
        @endif

        @auth
            <button
                type="button"
                class="bg-blue-500 text-white px-2 py-1 rounded mt-4"
                onclick="togglePlaylistSaveForm()"
            >
                Playlist opslaan
            </button>

            <!-- name saved playlist-->
            <form
                id="playlist-save-form"
                action="{{ route('playlist.save') }}"
                method="POST"
                class="mt-4"
                style="display:none;"
            >
                @csrf
                <input type="text" name="name" placeholder="Naam van de playlist" class="border p-1" required>
                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">
                    Save
                </button>
                <button
                    type="button"
                    class="text-red-600 px-2 py-1 rounded ml-2"
                    onclick="togglePlaylistSaveForm()"
                >
                    Cancel
                </button>
            </form>
        @endauth
    @else
        <p>Je playlist is leeg.</p>
    @endif

    <script>
        function togglePlaylistSaveForm() {
            const form = document.getElementById('playlist-save-form');
            if (!form) return;
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>
</x-layout>
