<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    // laat alle genres zien
    public function index()
    {
        $genres = Genre::all();
        return view('genre', ['genres' => $genres]);
    }

    // laat songs zien van een genre
    public function show($id)
    {
        $genre = Genre::with('songs')->findOrFail($id);
        return view('genre-songs', ['genre' => $genre]);
    }
}
