<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_admin'] = $validated['is_admin'] ?? false;

        User::create($validated);

        return redirect()->route('admin.users')->with('success', 'Gebruiker toegevoegd!');
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent self-delete
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Kan jezelf niet verwijderen.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Gebruiker verwijderd!');
    }
}

