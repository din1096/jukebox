<x-layout>
    <x-slot:heading>
        Songs in "{{ $genre->name }}"
    </x-slot:heading>

    @if ($genre->songs->count())
        <ul>
            @foreach ($genre->songs as $song)
                <li>
                    <a href="/songs/{{ $song->id }}" class="text-blue-500 hover:underline">
                        {{ $song->name }}
                    </a> by {{ $song->artist }}
                </li>
            @endforeach
        </ul>
    @else
        <p>No songs found for this genre.</p>
    @endif
</x-layout>
