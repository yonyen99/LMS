<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DelegationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $delegations = Delegation::with(['delegator', 'delegatee'])
            ->orderByDesc('created_at')
            ->paginate(10);
        $users = User::orderBy('name')->get(); // Include all users for dropdowns
        return view('delegations.index', compact('delegations', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id',
            'delegatee_id' => 'required|exists:users,id|different:delegator_id',
            'delegation_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            Delegation::create([
                'delegator_id' => $validated['delegator_id'],
                'delegatee_id' => $validated['delegatee_id'],
                'delegation_type' => $validated['delegation_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            return redirect()->route('delegations.index')
                ->with('success', 'Delegation created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('delegations.index')
                ->withErrors(['error' => 'Failed to create delegation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, Delegation $delegation)
    {
        $this->authorize('update', $delegation);

        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id',
            'delegatee_id' => 'required|exists:users,id|different:delegator_id',
            'delegation_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $delegation->update([
                'delegator_id' => $validated['delegator_id'],
                'delegatee_id' => $validated['delegatee_id'],
                'delegation_type' => $validated['delegation_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            return redirect()->route('delegations.index')
                ->with('success', 'Delegation updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('delegations.index')
                ->withErrors(['error' => 'Failed to update delegation: ' . $e->getMessage()])
                ->withInput();
        }
    }

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