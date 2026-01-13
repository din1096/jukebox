<?php

namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; 
use App\Models\Song;
use App\Models\SavedPlaylist;

class Playlist
{
    private $sessionKey = 'playlist';
    private $timeout = 180; // seconden

    public function getSongs()
    {
        // Check of de playlist verlopen is
        $created = Session::get($this->sessionKey . '_created');

        if ($created && (time() - $created > $this->timeout)) {
            // als de playlist verlopen is leegmaken
            Session::forget($this->sessionKey);
            Session::forget($this->sessionKey . '_created');
            return collect(); // lege collectie
        }
          // bij page refresh ook de timer vernieuwen
          if ($created) {
            Session::put($this->sessionKey . '_created', time());
        }

        $songIds = Session::get($this->sessionKey, []);
        return Song::whereIn('id', $songIds)->get();
    }

    // Voeg een song toe aan playlist
    public function addSong($songId)
    {
        $playlist = Session::get($this->sessionKey, []);

        if (!in_array($songId, $playlist)) {
            $playlist[] = $songId;
        }

        // vernieuw de tijd bij elke song die word toegevoegd
        Session::put($this->sessionKey . '_created', time());

        Session::put($this->sessionKey, $playlist);
    }

    // Verwijder een song
    public function removeSong($songId)
    {
        $playlist = Session::get($this->sessionKey, []);
        $playlist = array_filter($playlist, fn($id) => $id != $songId);

        Session::put($this->sessionKey, $playlist);
        // Reset ook de timer
        Session::put($this->sessionKey . '_created', time());
    }

    // Totale tijd van de playlist
    public function getTotalTime()
    {
        $songs = $this->getSongs();
        $totalSeconds = $songs->sum('duration'); // tijd in seconden

        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds % 60;

        return "Totale duur: {$minutes} minutes and {$seconds} seconds";
    }


// Save session playlist naar database
public function saveToDatabase(string $name)
{
    if (!Auth::check()) {
        return false;
    }

    $songs = Session::get($this->sessionKey, []);

    if (empty($songs)) {
        return false;
    }

    return SavedPlaylist::create([
        'user_id' => Auth::id(),
        'name'    => $name,
        'songs'   => array_values($songs),
    ]);
}

// Laad gesavde playlist in session
public function loadSavedPlaylist(int $id)
{
    $saved = SavedPlaylist::findOrFail($id);

    if ($saved->user_id !== Auth::id()) {
        abort(403);
    }

    Session::put($this->sessionKey, $saved->songs);
    Session::put($this->sessionKey . '_created', time());

    return $saved; 
}

// verwijder saved playlist
public function deleteSavedPlaylist(int $id)
{
    $saved = SavedPlaylist::findOrFail($id);

    if ($saved->user_id !== Auth::id()) {
        abort(403);
    }

    $saved->delete();

    return true;
}}