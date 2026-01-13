<?php

namespace App\Http\Controllers;

use App\Models\Song;

class SongController extends Controller
{
    // laat lijst van alle songs zien
    public function index()
    {
        return view('songs', [
            'songs' => Song::all()
        ]);
    }

    // laat 1 song zien bij ID
    public function show($id)
    {
        $song = Song::with('genre')->findOrFail($id);
        return view('song', ['song' => $song]);
    }
}