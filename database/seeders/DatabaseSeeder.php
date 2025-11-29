<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de prueba
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@cineplus.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Cajero Principal',
            'email' => 'cajero@cineplus.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        User::create([
            'name' => 'Cliente Ejemplo',
            'email' => 'cliente@cineplus.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        // Crear salas
        $room1 = Room::create([
            'name' => 'Sala 1 - Premium',
            'rows' => 8,
            'columns' => 10,
        ]);

        $room2 = Room::create([
            'name' => 'Sala 2 - 3D',
            'rows' => 6,
            'columns' => 8,
        ]);

        // Crear asientos para sala 1
        for ($row = 1; $row <= 8; $row++) {
            for ($col = 1; $col <= 10; $col++) {
                $type = 'regular';
                if ($row <= 2) $type = 'vip';
                if ($row == 8 && $col == 5) $type = 'disabled';

                Seat::create([
                    'room_id' => $room1->id,
                    'row_number' => $row,
                    'column_number' => $col,
                    'seat_code' => chr(64 + $row) . $col,
                    'type' => $type,
                ]);
            }
        }

        // Crear asientos para sala 2
        for ($row = 1; $row <= 6; $row++) {
            for ($col = 1; $col <= 8; $col++) {
                Seat::create([
                    'room_id' => $room2->id,
                    'row_number' => $row,
                    'column_number' => $col,
                    'seat_code' => chr(64 + $row) . $col,
                    'type' => 'regular',
                ]);
            }
        }

        // Crear películas
        $movie1 = Movie::create([
            'title' => 'Avengers: Endgame',
            'description' => 'Los Vengadores restantes deben encontrar una manera de recuperar a sus aliados para un enfrentamiento épico con Thanos.',
            'duration' => 181,
            'genre' => 'Acción, Aventura, Ciencia Ficción',
            'director' => 'Anthony Russo, Joe Russo',
            'cast' => 'Robert Downey Jr., Chris Evans, Mark Ruffalo, Chris Hemsworth',
            'poster_url' => 'https://via.placeholder.com/300x450/007bff/ffffff?text=Avengers',
            'release_date' => '2019-04-26',
        ]);

        $movie2 = Movie::create([
            'title' => 'Spider-Man: No Way Home',
            'description' => 'Peter Parker desenmascarado y no puede separar su vida normal de los enormes riesgos de ser un superhéroe.',
            'duration' => 148,
            'genre' => 'Acción, Aventura, Ciencia Ficción',
            'director' => 'Jon Watts',
            'cast' => 'Tom Holland, Zendaya, Benedict Cumberbatch',
            'poster_url' => 'https://via.placeholder.com/300x450/dc3545/ffffff?text=Spider-Man',
            'release_date' => '2021-12-17',
        ]);

        $movie3 = Movie::create([
            'title' => 'The Batman',
            'description' => 'Batman se adentra en la corrupción existente en la ciudad de Gotham y el vínculo que esta guarda con su propia familia.',
            'duration' => 176,
            'genre' => 'Acción, Crimen, Drama',
            'director' => 'Matt Reeves',
            'cast' => 'Robert Pattinson, Zoë Kravitz, Paul Dano',
            'poster_url' => 'https://via.placeholder.com/300x450/343a40/ffffff?text=Batman',
            'release_date' => '2022-03-04',
        ]);

        // Crear funciones
        $showtime1 = Showtime::create([
            'movie_id' => $movie1->id,
            'room_id' => $room1->id,
            'start_time' => now()->addDays(1)->setHour(18)->setMinute(0),
            'end_time' => now()->addDays(1)->setHour(21)->setMinute(1),
            'price' => 12.50,
        ]);

        $showtime2 = Showtime::create([
            'movie_id' => $movie2->id,
            'room_id' => $room2->id,
            'start_time' => now()->addDays(1)->setHour(20)->setMinute(0),
            'end_time' => now()->addDays(1)->setHour(22)->setMinute(28),
            'price' => 10.00,
        ]);

        $showtime3 = Showtime::create([
            'movie_id' => $movie3->id,
            'room_id' => $room1->id,
            'start_time' => now()->addDays(2)->setHour(16)->setMinute(30),
            'end_time' => now()->addDays(2)->setHour(19)->setMinute(26),
            'price' => 11.00,
        ]);
    }
}