<x-layout>
    <x-slot:heading>
        Songs List
    </x-slot:heading>

    <ul>
        @foreach ($songs as $song)
            <li class="mb-2"> 
                <!-- Link naar de song details -->
                <a href="{{ route('songs.show', $song->id) }}" class="text-blue-500 hover:underline">
                    {{ $song->name }}
                </a>
            </li>
        @endforeach
    </ul>
</x-layout>
