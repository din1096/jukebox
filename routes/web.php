<?php
use \Illuminate\Support\Arr;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SavedPlaylistController;
use Illuminate\Support\Facades\Route;
use App\Models\Song;
use App\Models\Genre;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', function () {
    return view('home');
})->name('home');


Route::get('/contact', function () {
    return view('contact');
});

Route::get('/genre', [GenreController::class, 'index'])->name('genre.index');

Route::get('/genre/{id}', [GenreController::class, 'show'])->name('genre.show');


Route::get('/songs', [SongController::class, 'index'])->name('songs.index');
Route::get('/songs/{id}', [SongController::class, 'show'])->name('songs.show');


Route::get('/playlist', [PlaylistController::class, 'index'])->name('playlist.index');

Route::post('/playlist/add/{id}', [PlaylistController::class, 'add'])->name('playlist.add');

Route::post('/playlist/remove/{id}', [PlaylistController::class, 'remove'])->name('playlist.remove');

Route::post('/playlist/save', [PlaylistController::class, 'save'])->middleware('auth')->name('playlist.save');

Route::get('/saved-playlists', [SavedPlaylistController::class, 'index'])
    ->middleware('auth')
    ->name('saved.playlists.index');

Route::post('/saved-playlists/{id}/load', [SavedPlaylistController::class, 'load'])
    ->middleware('auth')
    ->name('saved.playlists.load');

Route::delete('/saved-playlists/{id}', [SavedPlaylistController::class, 'destroy'])
    ->middleware('auth')
    ->name('saved.playlists.destroy');

Route::patch('/saved-playlists/{id}/rename', [SavedPlaylistController::class, 'rename'])
    ->middleware('auth')
    ->name('saved.playlists.rename');

Route::post('/saved-playlists/{id}/add-song', [SavedPlaylistController::class, 'addSong'])
    ->middleware('auth')->name('saved.playlists.addSong');

Route::delete('/saved-playlists/{playlistId}/song/{songId}', [SavedPlaylistController::class, 'removeSong'])
    ->middleware('auth')->name('saved.playlists.removeSong');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
