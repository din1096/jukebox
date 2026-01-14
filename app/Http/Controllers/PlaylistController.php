<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SessionPlaylist;
use App\Services\SavedPlaylist;

class PlaylistController extends Controller
{
    protected SessionPlaylist $sessionPlaylist;
    protected SavedPlaylist $savedPlaylists;

    public function __construct(SessionPlaylist $sessionPlaylist, SavedPlaylist $savedPlaylists)
    {
        $this->sessionPlaylist = $sessionPlaylist;
        $this->savedPlaylists = $savedPlaylists;
    }

    // Laat alle songs in de playlist zien met totale duur
    public function index()
    {
        $songs = $this->sessionPlaylist->getSongs();
        $totalTime = $this->sessionPlaylist->getTotalTime();

        return view('playlist', [
            'songs' => $songs,
            'totalTime' => $totalTime
        ]);
    }

    // Voeg een song toe aan de playlist
    public function add($id)
    {
        $this->sessionPlaylist->addSong((int) $id);
        return redirect()->back()->with('success', 'Song toegevoegd aan playlist!');
    }

    // Verwijder een song uit de playlist
    public function remove($id)
    {
        $this->sessionPlaylist->removeSong((int) $id);
        return redirect()->route('playlist.index')->with('success', 'Song verwijderd uit playlist!');
    }

    // Playlist opslaan in de database
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $songIds = $this->sessionPlaylist->getSongIds();
        $saved = $this->savedPlaylists->saveCurrentSessionPlaylist($request->name, $songIds);

        return redirect()->back()->with('status', $saved ? 'Playlist opgeslagen!' : 'Er is niets om op te slaan of je bent niet ingelogd.');
    }

    // Pagina met opgeslagen playlists
    public function savedIndex()
    {
        $lists = $this->savedPlaylists->listForCurrentUser();
        return view('saved_playlist', compact('lists'));
    }

    // Opgeslagen playlist laden
    public function load($id)
    {
        $loaded = $this->savedPlaylists->loadIntoSession((int) $id);

        return redirect()
            ->route('saved.playlists.index')  
            ->with('loadedPlaylist', $loaded)
            ->with('success', 'Playlist geladen!');
    }

    // Verwijder opgeslagen playlist
    public function destroySaved($id)
    {
        $this->savedPlaylists->delete((int) $id);
        return redirect()->back()->with('status', 'Playlist verwijderd.');
    }
}
