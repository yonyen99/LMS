<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DelegationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the delegations for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();
        $delegations = Delegation::with(['delegator', 'delegatee'])
            ->where(function ($query) use ($userId) {
                $query->where('delegator_id', $userId)
                      ->orWhere('delegatee_id', $userId);
            })
            ->orderByDesc('created_at')
            ->paginate(10);
        $users = User::orderBy('name')->get();
        $managers = User::role('Manager')->orderBy('name')->get();
        return view('delegations.index', compact('delegations', 'users', 'managers'));
    }

    /**
     * Store a newly created delegation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id',
            'delegatee_id' => 'required|exists:users,id|different:delegator_id',
            'delegation_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string',
        ]);

        // Ensure delegatee has Manager role
        $delegatee = User::findOrFail($validated['delegatee_id']);
        if (!$delegatee->hasRole('Manager')) {
            return redirect()->route('delegations.index')
                ->withErrors(['delegatee_id' => 'The selected delegatee must have the Manager role.'])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($validated) {
                Delegation::create($validated);
            });

            return redirect()->route('delegations.index')
                ->with('success', 'Delegation created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('delegations.index')
                ->withErrors(['error' => 'Failed to create delegation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update the specified delegation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Delegation  $delegation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Delegation $delegation)
    {
        $this->authorize('update', $delegation);

        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id',
            'delegatee_id' => 'required|exists:users,id|different:delegator_id',
            'delegation_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string',
        ]);

        // Ensure delegatee has Manager role
        $delegatee = User::findOrFail($validated['delegatee_id']);
        if (!$delegatee->hasRole('Manager')) {
            return redirect()->route('delegations.index')
                ->withErrors(['delegatee_id' => 'The selected delegatee must have the Manager role.'])
                ->withInput();
        }

        try {
            $delegation->update($validated);

            return redirect()->route('delegations.index')
                ->with('success', 'Delegation updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('delegations.index')
                ->withErrors(['error' => 'Failed to update delegation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified delegation from storage.
     *
     * @param  \App\Models\Delegation  $delegation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Delegation $delegation)
    {
        $this->authorize('delete', $delegation);
        try {
            $delegation->delete();
            return redirect()->route('delegations.index')
                ->with('success', 'Delegation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('delegations.index')
                ->withErrors(['error' => 'Failed to delete delegation: ' . $e->getMessage()]);
        }
    }
}