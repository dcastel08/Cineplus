<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $recentBookings = $user->bookings()
            ->with(['showtime.movie', 'seats'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('profile.show', compact('user', 'recentBookings'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Verificar contraseña actual si se intenta cambiar
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    public function bookingHistory()
    {
        $user = Auth::user();
        $bookings = $user->bookings()
            ->with(['showtime.movie', 'showtime.room', 'seats'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.booking-history', compact('bookings'));
    }
}