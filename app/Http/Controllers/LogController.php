<?php

namespace App\Http\Controllers;

use App\Models\LogError;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function errors()
    {
        $logs = LogError::all();
        return view('logs', compact('logs'));
    }
}
