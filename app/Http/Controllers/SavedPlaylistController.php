<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedPlaylist;
use App\Models\Song;
use App\Services\Playlist;
use Illuminate\Support\Facades\Auth;

class SavedPlaylistController extends Controller
{
    protected $playlist;

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    // Pagina met alle opgeslagen playlists
    public function index()
    {
        $lists = Auth::user()->savedPlaylists()->latest()->get();

        return view('saved_playlist', compact('lists'));
    }

    // Laad een opgeslagen playlist en zet songs in session
    public function load($id)
    {
        $saved = SavedPlaylist::findOrFail($id);

        if ($saved->user_id !== Auth::id()) {
            abort(403);
        }

        // Laad songs als collectie
        $songs = Song::whereIn('id', $saved->songs)->get();

        // Zet in session voor Blade
        session([
            'loadedPlaylist' => [
                'id' => $saved->id,
                'name' => $saved->name,
                'songs' => $songs
            ]
        ]);

        return redirect()->route('saved.playlists.index')
            ->with('success', "Playlist {$saved->name} is geladen!");
    }

    // Verwijder opgeslagen playlist
    public function destroy($id)
    {
        $saved = SavedPlaylist::findOrFail($id);

        if ($saved->user_id !== Auth::id()) {
            abort(403);
        }

        $saved->delete();

        return redirect()->back()->with('success', 'Playlist verwijderd!');
    }
    // geef een opgeslagen playlist een andere naam
    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $playlist = SavedPlaylist::findOrFail($id);

        if ($playlist->user_id !== auth()->id()) {
            abort(403);
        }

        $playlist->name = $request->name;
        $playlist->save();

        return redirect()->back()->with('success', 'Playlist naam is gewijzigd!');
}
    // Voeg een song toe aan opgeslagen playlist
    public function addSong(Request $request, $id)
    {
        $request->validate([
            'song_id' => 'required|integer|exists:songs,id'
        ]);

        $playlist = SavedPlaylist::findOrFail($id);
        if ($playlist->user_id !== Auth::id()) abort(403);

        $songs = is_array($playlist->songs) ? $playlist->songs : [];
        if (!in_array($request->song_id, $songs)) {
            $songs[] = (int)$request->song_id;
            $playlist->songs = $songs;
            $playlist->save();
        }

        // update session als deze playlist geladen is
        if (session('loadedPlaylist.id') === $playlist->id) {
            session(['loadedPlaylist.songs' => Song::whereIn('id', $playlist->songs)->get()]);
        }

        return back()->with('success', 'Song toegevoegd aan playlist!');
    }

    // Verwijder een song uit opgeslagen playlist
    public function removeSong($playlistId, $songId)
    {
        $playlist = SavedPlaylist::findOrFail($playlistId);
        if ($playlist->user_id !== Auth::id()) abort(403);

        $songs = is_array($playlist->songs) ? $playlist->songs : [];
        $songs = array_values(array_filter($songs, fn($id) => (int)$id !== (int)$songId));

        $playlist->songs = $songs;
        $playlist->save();

        if (session('loadedPlaylist.id') === $playlist->id) {
            session(['loadedPlaylist.songs' => Song::whereIn('id', $playlist->songs)->get()]);
        }

        return back()->with('success', 'Song verwijderd uit playlist!');
    }

}