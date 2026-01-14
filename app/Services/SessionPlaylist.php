<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Song;

class SessionPlaylist
{
    private string $sessionKey = 'playlist';
    private int $timeout = 600; // seconden


    public function getSongs()
    {
        $created = Session::get($this->sessionKey . '_created');

        // Check of de playlist verlopen is
        if ($created && (time() - $created > $this->timeout)) {
            Session::forget($this->sessionKey);
            Session::forget($this->sessionKey . '_created');
            return collect();
        }

        // als de pagina refreshed dan reset de tijd
        if ($created) {
            Session::put($this->sessionKey . '_created', time());
        }

        $songIds = Session::get($this->sessionKey, []);
        return Song::whereIn('id', $songIds)->get();
    }

     
    public function addSong(int $songId): void
    {
        $playlist = Session::get($this->sessionKey, []);

        if (!in_array($songId, $playlist)) {
            $playlist[] = $songId;
        }

        // reset de tijd als een song word toegevoed
        Session::put($this->sessionKey . '_created', time());
        Session::put($this->sessionKey, $playlist);
    }

    // verwijder een song 
    public function removeSong(int $songId): void
    {
        $playlist = Session::get($this->sessionKey, []);
        $playlist = array_filter($playlist, fn ($id) => (int) $id !== (int) $songId);

        Session::put($this->sessionKey, $playlist);
        // Reset ook de timer
        Session::put($this->sessionKey . '_created', time());
    }

    // Bereken de totale tijd
    public function getTotalTime(): string
    {
        $songs = $this->getSongs();
        $totalSeconds = $songs->sum('duration'); // tijd in seconden

        $minutes = (int) floor($totalSeconds / 60);
        $seconds = (int) ($totalSeconds % 60);

        return "Totale duur: {$minutes} minutes and {$seconds} seconds";
    }

    
    //Haal de song-ids uit de sessionplaylist.
    
    public function getSongIds(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Zet song-ids in de session-playlist en reset de timer.
     */
    public function setSongIds(array $songIds): void
    {
        Session::put($this->sessionKey, array_values($songIds));
        Session::put($this->sessionKey . '_created', time());
    }
}

