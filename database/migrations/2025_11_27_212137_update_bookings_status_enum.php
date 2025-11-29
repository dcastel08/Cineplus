<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Para MySQL - Actualizar el ENUM para incluir 'used'
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled', 'used') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revertir - quitar 'used' del ENUM
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'");
    }
};