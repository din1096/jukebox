<x-layout>
    <x-slot:heading>Opgeslagen Playlists</x-slot:heading>

    <!-- Saved playlists list -->
    @foreach($lists as $list)
        <div class="border p-3 mb-3">
            <strong>{{ $list->name }}</strong>

            <!-- laad playlist -->
            <form action="{{ route('saved.playlists.load', $list->id) }}" method="POST" class="inline">
                @csrf
                <button class="text-blue-600 ml-3">Laden</button>
            </form>

            <!-- delete Playlist -->
            <form action="{{ route('saved.playlists.destroy', $list->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button class="text-red-600 ml-3">Verwijder</button>
            </form>
            
            <!-- Rename Playlist -->
             <button type="button"class="bg-blue-500 text-white px-2 py-1 rounded ml-2 mt-2"onclick="toggleRenameForm({{ $list->id }})">
                Rename</button>
            <form
                id="rename-form-{{ $list->id }}"
                action="{{ route('saved.playlists.rename', $list->id) }}"
                method="POST"
                class="mt-2"
                style="display:none;"
            >
                @csrf
                @method('PATCH')
                <input type="text" name="name" value="{{ $list->name }}" class="border p-1" required>
                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded ml-2">Save</button>
                <button
                    type="button"
                    class="text-red-600 px-2 py-1 rounded ml-2"
                    onclick="toggleRenameForm({{ $list->id }})"
                >
                    Cancel
                </button>
            </form>
        </div>
    @endforeach

    @if(session('loadedPlaylist'))
        @php $loadedPlaylist = session('loadedPlaylist'); @endphp

        <div class="alert alert-success mt-4">
            Playlist <strong>{{ $loadedPlaylist['name'] }}</strong> is geladen!
        </div>

        <h3 class="text-xl font-semibold mt-3">Songs in deze playlist:</h3>
        <ul class="list-disc ml-5">
            @foreach($loadedPlaylist['songs'] as $song)
                <li class="mb-2">
                    <!-- Song name -->
                    <a href="{{ route('songs.show', $song->id) }}" class="text-blue-500 hover:underline">
                        {{ $song->name }}
                    </a>
                    <span class="text-gray-600 ml-1">({{\Carbon\CarbonInterval::seconds($song->duration)->cascade()->forHumans(['short' => true]) }})</span>

                    <!-- delete song from playlist -->
                    @if(isset($loadedPlaylist['id']))
                        <form action="{{ route('saved.playlists.removeSong', [$loadedPlaylist['id'], $song->id]) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">delete</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        <!-- Add song to playlist -->
        @if(isset($loadedPlaylist['id']))
            <form action="{{ route('saved.playlists.addSong', $loadedPlaylist['id']) }}" method="POST" class="mt-3">
                @csrf
                <select name="song_id" required>
                    @foreach(\App\Models\Song::all() as $song)
                        <option value="{{ $song->id }}">{{ $song->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded ml-2">add song</button>
            </form>
        @endif
        <!-- bereken total time of playlist -->
        <form method="GET" action="{{ route('saved.playlists.index') }}" class="mt-4">
            <button type="submit" name="show_total" value="1" class="text-red-500 hover:underline">
                Total Time
            </button>
        </form>

        @if(request('show_total'))
            @php
                $totalSeconds = collect($loadedPlaylist['songs'])->sum('duration');
                $totalTime = \Carbon\CarbonInterval::seconds($totalSeconds)->cascade()->forHumans(['short' => true]);
            @endphp
            <p class="mt-2 font-semibold">
                {{ $totalTime }}
            </p>
        @endif
    @endif

    <script>
        function toggleRenameForm(id) {
            const form = document.getElementById('rename-form-' + id);
            if (!form) return;
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>
</x-layout> 