<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'genre',
        'director',
        'cast',
        'poster_url',
        'trailer_url',
        'release_date',
        'is_active',
    ];

    // 👇 AÑADIDO: CAST PARA FECHAS
    protected $casts = [
        'release_date' => 'date',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
