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
        <p class="mb-4 font-semibold">
            Totale duur: {{ $totalTime }} 
        </p>

        <ul>
            @foreach($songs as $song)
                <li class="mb-2">
                    <a href="/songs/{{ $song->id }}" class="text-blue-500 hover:underline">
                        {{ $song->name }}
                    </a>
                    <span class="text-gray-600 ml-2">({{\Carbon\CarbonInterval::seconds($song->duration)->cascade()->forHumans(['short' => true]) }})</span>  

                    <form action="{{ route('playlist.remove', $song->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:underline ml-2">Verwijder</button>
                    </form>
                </li>
            @endforeach
        </ul>

        @auth
            <form action="{{ route('playlist.save') }}" method="POST" class="mt-4">
                @csrf
                <input type="text" name="name" placeholder="Naam van playlist" class="border p-1" required>
                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">
                    Playlist opslaan
                </button>
            </form>
        @endauth

    @else
        <p>Je playlist is leeg.</p>
    @endif
</x-layout>