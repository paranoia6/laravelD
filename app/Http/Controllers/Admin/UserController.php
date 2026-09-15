<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereNotIn('role', ['admin', 'super_admin']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('email', 'like', "%{$search}%");
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        }

        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->type === 'test') {
            $query->where('is_test', true);
        }

        if ($request->type === 'normal') {
            $query->where('is_test', false);
        }

        $users = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->ensureManageableUser($user);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $this->ensureManageableUser($user);

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'وضعیت کاربر با موفقیت تغییر کرد.');
    }

    private function ensureManageableUser(User $user): void
    {
        if (in_array($user->role->value, ['admin', 'super_admin'], true)) {
            abort(403);
        }
    }
}
