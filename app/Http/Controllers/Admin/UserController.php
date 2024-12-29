<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($role = $request->get('role')) {
            $query->role($role);
        }

        $users = $query->with('roles')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Eager load roles and latest activities
        $user->load(['roles', 'activities' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $user = new User;
        return view('admin.users.add_edit', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,mod',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Log activity
        $this->logActivity(
            'create_user',
            'Created user: ' . $user->name,
            [
                'user_id' => $user->id,
                'title' => $user->name,
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Tạo người dùng thành công');
    }

    public function edit(User $user)
    {
        return view('admin.users.add_edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,mod',
        ]);

        // Don't allow users to change their own role or status
        if ($user->id === auth()->id()) {
            unset($validated['role'], $validated['status']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['role']) && $user->id !== auth()->id()) {
            $user->syncRoles([$validated['role']]);
        }

        // Log activity
        $this->logActivity(
            'update_user',
            'Updated user: ' . $user->name,
            [
                'user_id' => $user->id,
                'title' => $user->name,
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Cập nhật người dùng thành công');
    }

    public function destroy(User $user)
    {
        // Log activity
        $this->logActivity(
            'delete_user',
            'Deleted user: ' . $user->name,
            [
                'user_id' => $user->id,
                'title' => $user->name,
            ]
        );

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công');
    }

    public function activity(Request $request)
    {
        $query = Activity::with('user')->latest();

        // Apply filters
        if ($userId = $request->get('user')) {
            $query->where('user_id', $userId);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($fromDate = $request->get('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate = $request->get('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $activities = $query->paginate(20)->withQueryString();
        $users = User::all(); // For filter dropdown

        return view('admin.users.activity', compact('activities', 'users'));
    }
}