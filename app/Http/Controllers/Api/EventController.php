<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventNotification;

class EventController extends Controller
{
    
    // GET
    public function index(Request $request)
{
    $user = $request->user();

    if ($user->role == 'admin') {
        $data = Event::all();
    } else {
        $data = Event::where('role', $user->role)->get(); 
    }

    return response()->json([
        'data' => $data
    ]);
}
    

    // POST
    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_kegiatan' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'jumlah' => 'nullable|integer',
        'tanggal' => 'required|date',
        'role' => 'required|in:kandang,gudang,reseller' 
    ]);

    $validated['user_id'] = $request->user()->id;
    $validated['status'] = 'pending';

    $event = Event::create($validated);

    $users = \App\Models\User::where('role', $validated['role'])->get();

    foreach ($users as $user) {
    $user->notify(new EventNotification($event));
}

    return response()->json([
        'message' => 'Event berhasil dibuat',
        'data' => $event
    ]);
}

public function show(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $user = $request->user();

    if ($user->role !== 'admin' && $event->role !== $user->role) {
        return response()->json([
            'message' => 'Akses ditolak'
        ], 403);
    }

    return response()->json([
        'data' => $event
    ]);
}

    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $validated = $request->validate([
        'nama_kegiatan' => 'sometimes|required|string|max:255',
        'deskripsi' => 'nullable|string',
        'jumlah' => 'nullable|integer',
        'tanggal' => 'sometimes|required|date',
        'role' => 'sometimes|required|in:kandang,gudang,reseller'
    ]);

    $event->update($validated);

    return response()->json([
        'message' => 'Event berhasil diupdate',
        'data' => $event
    ]);
}

    // UPDATE STATUS (ADMIN)
    public function updateStatus(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:pending,acc,reject'
    ]);

    $event->update($validated);

    return response()->json([
        'message' => 'Status event diupdate',
        'data' => $event
    ]);
}

    // DELETE
    public function destroy($id)
{
    $event = Event::findOrFail($id);
    $event->delete();

    return response()->json([
        'message' => 'Event dihapus'
    ]);
}
    }
