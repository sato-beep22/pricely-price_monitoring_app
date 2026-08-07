<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::with('shop')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Update user roles or information.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,buyer,farmer',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('status', "User {$user->name} role updated to {$request->role}.");
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account from the admin panel.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "User {$userName} deleted successfully.");
    }
}
