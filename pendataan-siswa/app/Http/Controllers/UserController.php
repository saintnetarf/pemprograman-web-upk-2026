<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'q'));
    }

    public function create(): View
    {
        return view('users.create', [
            'user' => new User(),
            'roles' => collect(Role::cases())->mapWithKeys(fn (Role $r) => [$r->value => $r->label()]),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->only(['name', 'email', 'password']));
        $user->role = $request->enum('role', Role::class);
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => collect(Role::cases())->mapWithKeys(fn (Role $r) => [$r->value => $r->label()]),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->safe()->only(['name', 'email']));
        $user->role = $request->enum('role', Role::class);

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}

