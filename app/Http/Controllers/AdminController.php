<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Verificación simple y directa
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos de administrador.');
        }

        $user = auth()->user();
        return view('admin.dashboard', compact('user'));
    }
}