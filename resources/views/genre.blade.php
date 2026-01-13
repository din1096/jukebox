<x-layout>
    <x-slot:heading>
        Genre page
    </x-slot:heading>

    <h1>Welcome to the genre page</h1>

    <ul>
        @foreach ($genres as $genre)
            <li>
                <a href="/genre/{{ $genre->id }}" class="text-blue-500 hover:underline">
                    {{ $genre->name }}
                </a>
            </li>
        @endforeach
    </ul>
</x-layout>