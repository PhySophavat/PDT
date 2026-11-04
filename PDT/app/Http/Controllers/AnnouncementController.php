<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $audience = $request->input('audience');

        $announcements = Announcement::when($audience, fn ($q) => $q->where('target_audience', $audience))
            ->latest()
            ->paginate(10);

        return view('announcement.index', compact('announcements', 'audience'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'target_audience' => 'required|in:all,teachers,students,parents',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['publish_date'] = $validated['publish_date'] ?? Carbon::today();
        $validated['is_active'] = $request->has('is_active');

        Announcement::create($validated);

        return redirect()->route('announcements.index')->with('success', 'បានផ្សាយដំណឹងថ្មីដោយជោគជ័យ!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'target_audience' => 'required|in:all,teachers,students,parents',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $announcement->update($validated);

        return redirect()->route('announcements.index')->with('success', 'បានកែប្រែដំណឹងដោយជោគជ័យ!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'បានលុបដំណឹងដោយជោគជ័យ!');
    }
}
