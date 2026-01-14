<?php

namespace App\Services;

use App\Models\SavedPlaylist as SavedPlaylistModel;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SavedPlaylist
{
    /**
     * sla de tijdelijke playlist als saedplaylist in de database
     */
     public function saveCurrentSessionPlaylist(string $name, array $songIds): ?SavedPlaylistModel
    {
        if (!Auth::check()) {
            return null;
        }

        if (empty($songIds)) {
            return null;
        }

        return SavedPlaylistModel::create([
            'user_id' => Auth::id(),
            'name'    => $name,
            'songs'   => array_values($songIds),
        ]);
    }

    /**
     * Haal een opgeslagen playlist van de huidige gebruiker op.
     */
    public function loadIntoSession(int $id): SavedPlaylistModel
    {
        $saved = SavedPlaylistModel::findOrFail($id);

        if ($saved->user_id !== Auth::id()) {
            abort(403);
        }

        return $saved;
    }

    /**
     * Verwijder een opgeslagen playlist.
     */
    public function delete(int $id): bool
    {
        $saved = SavedPlaylistModel::findOrFail($id);

        if ($saved->user_id !== Auth::id()) {
            abort(403);
        }

        // Als deze playlist momenteel als 'loadedPlaylist' in de session staat, verwijder die dan ook
        if (Session::get('loadedPlaylist.id') === $saved->id) {
            Session::forget('loadedPlaylist');
        }

        $saved->delete();

        return true;
    }

    /**
     * Geef alle opgeslagen playlists van de huidige gebruiker terug.
     */
    public function listForCurrentUser()
    {
        return Auth::user()
            ? Auth::user()->savedPlaylists()->latest()->get()
            : collect();
    }

    /**
     * Hernoem een opgeslagen playlist van de huidige gebruiker.
     */
    public function rename(int $id, string $name): SavedPlaylistModel
    {
        $playlist = SavedPlaylistModel::findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->name = $name;
        $playlist->save();

        // Als deze playlist geladen is, werk dan ook de naam in de session bij
        if (Session::get('loadedPlaylist.id') === $playlist->id) {
            Session::put('loadedPlaylist.name', $playlist->name);
        }

        return $playlist;
    }

    /**
     * Voeg een song toe aan een opgeslagen playlist.
     */
    public function addSongToSaved(int $playlistId, int $songId): SavedPlaylistModel
    {
        $playlist = SavedPlaylistModel::findOrFail($playlistId);

        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $songs = is_array($playlist->songs) ? $playlist->songs : [];

        if (!in_array($songId, $songs)) {
            $songs[] = $songId;
            $playlist->songs = array_values($songs);
            $playlist->save();
        }

        return $playlist;
    }

    /**
     * Verwijder een song uit een opgeslagen playlist.
     */
    public function removeSongFromSaved(int $playlistId, int $songId): SavedPlaylistModel
    {
        $playlist = SavedPlaylistModel::findOrFail($playlistId);

        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $songs = is_array($playlist->songs) ? $playlist->songs : [];

        $songs = array_values(
            array_filter($songs, fn ($id) => (int) $id !== (int) $songId)
        );

        $playlist->songs = $songs;
        $playlist->save();

        return $playlist;
    }

    /**
     * Werk de session 'loadedPlaylist' bij voor de UI, o.b.v. een SavedPlaylist.
     */
    public function syncLoadedPlaylistSession(SavedPlaylistModel $playlist): void
    {
        $songs = Song::whereIn('id', $playlist->songs)->get();

        Session::put('loadedPlaylist', [
            'id'    => $playlist->id,
            'name'  => $playlist->name,
            'songs' => $songs,
        ]);
    }
}

