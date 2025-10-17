<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['index', 'create', 'store', 'destroy']);
    }

    public function create()
    {
        return view('users.create');
    }

    public function index(Request $request)
    {
        $users = User::query()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Password hashing is handled by User model cast ('password' => 'hashed')
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            // Paksa semua user baru sebagai user biasa (bukan admin)
            'is_admin' => 0,
        ]);

        return redirect()->route('users.create')->with('status', 'user-created');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('status', 'user-deleted');
    }
}
