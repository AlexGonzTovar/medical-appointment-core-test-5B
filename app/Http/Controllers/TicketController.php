<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = \App\Models\Ticket::with('user')->orderBy('created_at', 'desc')->get();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        \App\Models\Ticket::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Abierto',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado exitosamente.');
    }
}
