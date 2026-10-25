<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));

        return view('admin.users.index', [
            'users' => User::withCount('articles')
                ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
            'roles' => Role::cases(),
            'q' => $q,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::enum(Role::class)]]);

        if ($user->is($request->user()) && $data['role'] !== Role::Admin->value) {
            return back()->withErrors(['role' => 'You cannot remove your own administrator role.']);
        }

        $user->role = Role::from($data['role']);
        $user->save();

        return back()->with('status', "{$user->name} is now {$user->role->label()}.");
    }
}
