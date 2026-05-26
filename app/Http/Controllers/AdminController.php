<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $validated['is_admin'] = $validated['is_admin'] ?? false;

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Gebruiker bijgewerkt!');
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

    /**
     * Display notifications overview.
     */
    public function notifications(): View
    {
        $notifications = Notification::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markNotificationRead(Notification $notification): RedirectResponse
    {
        $notification->update(['gelezen' => true]);

        return redirect()->back()->with('success', 'Melding gemarkeerd als gelezen.');
    }

    /**
     * Mark a notification as unread.
     */
    public function markNotificationUnread(Notification $notification): RedirectResponse
    {
        $notification->update(['gelezen' => false]);

        return redirect()->back()->with('success', 'Melding gemarkeerd als ongelezen.');
    }
}

