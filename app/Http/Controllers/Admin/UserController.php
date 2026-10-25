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
        $unverifiedOnly = $request->query('status') === 'unverified';

        return view('admin.users.index', [
            'users' => User::withCount('articles')
                ->when($unverifiedOnly, fn ($query) => $query->whereNull('email_verified_at'))
                ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
            'roles' => Role::cases(),
            'q' => $q,
            'unverifiedOnly' => $unverifiedOnly,
            'unverifiedCount' => User::whereNull('email_verified_at')->count(),
            'staleUnverifiedCount' => $this->staleUnverified()->count(),
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

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['role' => 'You cannot delete your own account here.']);
        }

        if ($user->role !== Role::Member) {
            return back()->withErrors(['role' => 'Change this person to Member before deleting the account.']);
        }

        $user->delete();

        return back()->with('status', "{$user->name} was deleted.");
    }

    /** Deletes members who never confirmed their email and registered more than 7 days ago. */
    public function destroyUnverified(): RedirectResponse
    {
        $count = $this->staleUnverified()->delete();

        return back()->with('status', "Deleted {$count} unverified ".str('account')->plural($count).'.');
    }

    private function staleUnverified()
    {
        return User::whereNull('email_verified_at')
            ->where('role', Role::Member->value)
            ->where('created_at', '<', now()->subDays(7));
    }
}
