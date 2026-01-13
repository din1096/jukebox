<x-layout>
    <x-slot:heading>
        Song information
    </x-slot:heading>

    <h2 class="font-bold text-lg">{{ $song->name }}</h2>
    <p>
        This song genre is {{ $song->genre->name }} and artist is {{ $song->artist }} and duration is {{ $song->duration }} seconds.
    </p>

    <!-- Voeg toe aan playlist button -->
    <form action="{{ route('playlist.add', $song->id) }}" method="POST">
        @csrf
        <button type="submit">Voeg toe aan playlist</button>
    </form>
</x-layout>