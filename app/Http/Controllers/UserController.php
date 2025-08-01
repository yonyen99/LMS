<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-user|edit-user|delete-user', ['only' => ['index','show']]);
        $this->middleware('permission:create-user', ['only' => ['create','store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('users.index', [
        'users' => User::latest()->paginate(6),
        'totalUsers' => User::count(),
        'activeUsers' => User::where('is_active', true)->count(),
        'adminUsers' => User::role('Admin')->count(),
        'newUsers' => User::where('created_at', '>=', now()->subDays(30))->count()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::pluck('name')->all(),
            'departments' => Department::pluck('name', 'id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $input = $request->validated(); 
        $input['password'] = Hash::make($request->password);

        if ($request->hasFile('images')) {
            $input['images'] = $request->file('images')->store('users', 'public');
        }

        $user = User::create($input);
        $user->assignRole($request->roles);

        return redirect()->route('users.index')
                ->withSuccess('New user added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // Check Only Super Admin can update his own Profile
        if ($user->hasRole('Super Admin')){
            if($user->id != auth()->user()->id){
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }

        return view('users.edit', [
            'user' => $user,
            'roles' => Role::pluck('name')->all(),
            'userRoles' => $user->roles->pluck('name')->all(),
            'departments' => Department::pluck('name', 'id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $input = $request->validated();

        if(!empty($request->password)){
            $input['password'] = Hash::make($request->password);
        }else{
            $input = $request->except('password');
        }

        // Handle image upload
        if ($request->hasFile('images')) {
            // Delete old image if exists
            if ($user->images && Storage::disk('public')->exists($user->images)) {
                Storage::disk('public')->delete($user->images);
            }
            $input['images'] = $request->file('images')->store('users', 'public');
        }

        $user->update($input);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
                ->withSuccess('User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // About if user is Super Admin or user is deleting himself
        if ($user->hasRole('Super Admin') || $user->id == auth()->user()->id)
        {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }

        // Delete user image if exists
        if ($user->images) {
            Storage::disk('public')->delete($user->images);
        }

        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('users.index')
                ->withSuccess('User deleted successfully.');
    }

    public function updateImage(Request $request, User $user)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old image if it exists
        if ($user->images && Storage::disk('public')->exists($user->images)) {
            Storage::disk('public')->delete($user->images);
        }

        // Store new image
        $path = $request->file('image')->store('users', 'public');
        $user->images = $path;
        $user->save();

        return redirect()->back()->with('success', 'Profile image updated successfully.');
    }

}