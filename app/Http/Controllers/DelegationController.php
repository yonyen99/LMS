<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DelegationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the delegations.
     * This method retrieves all delegations and their associated users,
     * ordered by the most recent delegation first.
     *
     * @return \Illuminate\View\View
     */
    

    public function index()
    {
        $delegations = Delegation::with(['delegator', 'delegatee'])
            ->orderByDesc('created_at')
            ->paginate(10);
        $users = User::orderBy('name')->get(); // Include all users for dropdowns
        return view('delegations.index', compact('delegations', 'users'));
    }

    /**
     * Show the form for creating a new delegation.
     * This method retrieves all users to populate the dropdown for delegator and delegatee.
     *
     * @return \Illuminate\View\View
     */


    public function store(Request $request)
    {
        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id',
            'delegatee_id' => 'required|exists:users,id|different:delegator_id',
            'delegation_type' => 'required|string|max:255', // Changed from 'type' to 'delegation_type'
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

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
     * Show the form for editing the specified delegation.
     * This method retrieves a specific delegation record for editing.
     * It checks if the user has permission to edit the record and then returns the edit view
     * with the delegation data.
     *
     * @param Delegation $delegation
     * @return \Illuminate\View\View
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

    /**
     * Remove the specified delegation from storage.
     * This method deletes a delegation record and redirects back to the index with a success message.
     *
     * @param Delegation $delegation
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
