<?php

namespace App\Http\Controllers;

use App\Models\AdminTodo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminTodoController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->route('token');

        $todos = AdminTodo::orderBy('date', 'desc')->get()->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y');
        });

        return view('admin.dashboard', [
            'todos' => $todos,
            'token' => $token
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'date' => 'required|date',
        ]);

        AdminTodo::create($request->only('text', 'date'));

        return redirect()->back();
    }

    public function destroy($token, AdminTodo $todo)
    {
        $todo->delete();

        // Redirect kembali ke dashboard dengan menyertakan token
        return redirect()->route('admin.dashboard', ['token' => $token]);
    }
}
