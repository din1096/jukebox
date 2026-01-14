<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Services\SavedPlaylist;
use Illuminate\Support\Facades\Auth; 

class SavedPlaylistController extends Controller
{
    protected SavedPlaylist $savedPlaylists;

    public function __construct(SavedPlaylist $savedPlaylists)
    {
        $this->savedPlaylists = $savedPlaylists;
    }

    // Pagina met alle opgeslagen playlists
    public function index()
    {
        $lists = $this->savedPlaylists->listForCurrentUser();
        return view('saved_playlist', compact('lists'));
    }

    // Laad een opgeslagen playlist en zet songs in session
    public function load($id)
    {
        $saved = $this->savedPlaylists->loadIntoSession((int) $id);
        $this->savedPlaylists->syncLoadedPlaylistSession($saved);

        return redirect()->route('saved.playlists.index')
            ->with('success', "Playlist {$saved->name} is geladen!");
    }

    // Verwijder opgeslagen playlist
    public function destroy($id)
    {
        $this->savedPlaylists->delete((int) $id);

        return redirect()->back()->with('success', 'Playlist verwijderd!');
    }
    // geef een opgeslagen playlist een andere naam
    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $playlist = $this->savedPlaylists->rename((int) $id, $request->name);

        return redirect()->back()->with('success', 'Playlist naam is gewijzigd!');
}
    // Voeg een song toe aan opgeslagen playlist
    public function addSong(Request $request, $id)
    {
        $request->validate([
            'song_id' => 'required|integer|exists:songs,id'
        ]);

        $playlist = $this->savedPlaylists->addSongToSaved((int) $id, (int) $request->song_id);

        // update session als deze playlist geladen is
        if (session('loadedPlaylist.id') === $playlist->id) {
            session(['loadedPlaylist.songs' => Song::whereIn('id', $playlist->songs)->get()]);
        }

        return back()->with('success', 'Song toegevoegd aan playlist!');
    }

    // Verwijder een song uit opgeslagen playlist
    public function removeSong($playlistId, $songId)
    {
        $playlist = $this->savedPlaylists->removeSongFromSaved((int) $playlistId, (int) $songId);

        if (session('loadedPlaylist.id') === $playlist->id) {
            session(['loadedPlaylist.songs' => Song::whereIn('id', $playlist->songs)->get()]);
        }

        return back()->with('success', 'Song verwijderd uit playlist!');
    }

}