<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charger;
use App\Models\Transformer;
use App\Models\ChargingSession;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(){
        $transformers = Transformer::all();

        $totalChargers = Charger::count();
        
        $activeChargers = Charger::where('status', 'busy')->count();
        $activeSessions = ChargingSession::whereNull('ended_at')->count();

        $sessions = ChargingSession::with(['charger.transformer', 'user'])
            ->whereNull('ended_at')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'transformers',
            'totalChargers',
            'activeChargers',
            'activeSessions',
            'sessions'
        ));
    }
}