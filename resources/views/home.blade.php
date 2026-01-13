<x-layout>
    <x-slot:heading>
        Home page
    </x-slot:heading>

    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Welkom bij de Jukebox!</h1>

        <p>
            Welkom dit is jukebox de plek waar je je favoriete muziek kunt ontdekken, beluisteren en verzamelen.
            Als bezoeker kun je de verschillende <strong>genres</strong> muziek verkennen en door de
            <strong>songlijst</strong> kijken om nieuwe songs te vinden.
        </p>

        <p>
            Heb je een account? Dan kun je inloggen om je eigen <strong>playlist</strong> samen te stellen.
            Voeg makelijk liedjes toe aan je persoonlijke Playlist
            van je verzameling. Zo maak je in een paar klikken je eigen muzikale ervaring.
        </p>

        <p>
            Nog geen account? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Registreer je hier</a>
            en start vandaag nog met het bouwen van jouw perfecte playlist.
        </p>

        <p>
            Klaar om te beginnen? Bekijk de <a href="{{ route('songs.index') }}" class="text-blue-500 hover:underline">songlijst</a>
            of ontdek alle <a href="{{ route('genre.index') }}" class="text-blue-500 hover:underline">genres</a>!
        </p>
    </div>
</x-layout>
