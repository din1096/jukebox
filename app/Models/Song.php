<?php

namespace App\Models;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Song extends Model
{
    use HasFactory;

    protected $table = 'songs';  // zorgt dat laravel de goede table pakt
    protected $fillable = ['name', 'artist', 'duration', 'genre_id'];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}