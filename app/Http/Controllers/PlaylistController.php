<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Playlist;

class PlaylistController extends Controller
{
    protected $playlist;

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    // Laat alle songs in de playlist zien met totale duur
    public function index()
    {
        $songs = $this->playlist->getSongs();
        $totalTime = $this->playlist->getTotalTime();

        return view('playlist', [
            'songs' => $songs,
            'totalTime' => $totalTime
        ]);
    }

    // Voeg een song toe aan de playlist
    public function add($id)
    {
        $this->playlist->addSong($id);
        return redirect()->back()->with('success', 'Song toegevoegd aan playlist!');
    }

    // Verwijder een song uit de playlist
    public function remove($id)
    {
        $this->playlist->removeSong($id);
        return redirect()->route('playlist.index')->with('success', 'Song verwijderd uit playlist!');
    }

    // Playlist opslaan in database
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $saved = $this->playlist->saveToDatabase($request->name);

        return redirect()->back()->with('status', $saved ? 'Playlist opgeslagen!' : 'Er is niets om op te slaan.');
    }

    // Pagina met opgeslagen playlists
    public function savedIndex()
    {
        $lists = auth()->user()->savedPlaylists()->latest()->get();
        return view('saved_playlist', compact('lists'));
    }

    // Opgeslagen playlist laden
    public function load($id)
    {
        $loaded = $this->playlist->loadSavedPlaylist((int)$id);

        return redirect()
            ->route('saved.playlists.index')  
            ->with('loadedPlaylist', $loaded)
            ->with('success', 'Playlist geladen!');
    }

    // Verwijder opgeslagen playlist
    public function destroySaved($id)
    {
        $this->playlist->deleteSavedPlaylist((int)$id);
        return redirect()->back()->with('status', 'Playlist verwijderd.');
    }
}
