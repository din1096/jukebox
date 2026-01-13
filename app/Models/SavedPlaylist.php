<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPlaylist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'songs'];

    protected $casts = [
        'songs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    public function songs()
{
    return $this->belongsToMany(Song::class, 'playlist_song'); 
}
}
