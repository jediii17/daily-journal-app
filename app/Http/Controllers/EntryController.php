<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only('search');

        $entries = $request->user()
            ->entries()
            ->with('user')
            ->filter($filters)
            ->latest('date')
            ->get();

        return Inertia::render('entries/Index', [
            'entries' => $entries,
            'search' => $filters['search'] ?? null,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'required|string',
        ]);

        // If the validation fails, return the errors and the input
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // If the validation passes, validate the data
        $validated = $validator->validated();

        // Validation to limit adding journal entries in the future date
        if ($validated['date'] > now()) {
            return redirect()->back()->withErrors([
                'date' => 'You can only journal up to today. Try choosing today or an earlier date.',
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create the journal entry
            $request->user()->entries()->create($validated);

            // Commit the transaction
            DB::commit();

            // Return a friendly success message
            return redirect()->back()->with(
                'success',
                'Your thoughts have been saved. Keep journaling—you\'re doing great.'
            );
        } catch (\Exception $e) {

            DB::rollBack(); // Roll back the transaction if an error occurs
            return redirect()->back()->with(
                'error',
                'We couldn\'t save this entry just now. Please try again in a moment.'
            );
        }

        // Return a success message if the transaction is successful
        return redirect()->back()->with(
            'success',
            'Your thoughts have been saved. Keep journaling—you\'re doing great.'
        );
    }

    public function update(Request $request, Entry $entry)
    {
        // If the entry does not belong to the user, abort the request
        if ($entry->user_id !== $request->user()->id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Validation to limit updating journal entries in the future date
        if ($validated['date'] > now()) {
            return redirect()->back()->withErrors([
                'date' => 'You can only journal up to today. Try choosing today or an earlier date.',
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Update the journal entry
            $entry->update($validated);

            // Commit the transaction
            DB::commit();

            // Return a friendly success message
            return redirect()->back()->with(
                'success',
                'Your entry has been updated. Thanks for taking a moment to reflect.'
            );
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction if an error occurs
            $entry->update($validated);
            return redirect()->back()->with(
                'error',
                'We couldn\'t update this entry just now. Please try again in a moment.'
            );
        }

        // Return a success message if the transaction is successful
        return redirect()->back()->with(
            'success',
            'Your entry has been updated. Thanks for taking a moment to reflect.'
        );
    }

    public function show(Entry $entry, Request $request)
    {
        // If the entry does not belong to the user, abort the request
        if ($entry->user_id !== $request->user()->id()) {
            abort(403);
        }

        // Render the show view with the entry
        return Inertia::render('entries/Show', [
            'entry' => $entry
        ]);
    }
}
