<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Request as ModelsRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $latestRequest = ModelsRequest::with('visitors')->latest()->take(5)->get();

        $upcomingVisit = ModelsRequest::with('visitors')
            ->where('status', 'accepted')
            ->whereDate('start_date', '>', Carbon::now()->toDateString())
            ->orWhere(function ($query) {
                $query->whereDate('start_date', '=', Carbon::now()->toDateString())
                      ->whereTime('start_date', '>', Carbon::now()->toTimeString());
            })
            ->oldest()->take(5)->get();
        // dd($upcomingVisit);

        return Inertia::render('Dashboard', [
            'dataTotals' => [
                'all' => ModelsRequest::count(),
                'requested' => ModelsRequest::where('status', 'requested')->count(),
                'accepted' => ModelsRequest::where('status', 'accepted')->count(),
                'rejected' => ModelsRequest::where('status', 'rejected')->count(),
                'finished' => ModelsRequest::where('status', 'finished')->count(),
                'missed' => ModelsRequest::where('status', 'missed')->count()
            ],
            'latestRequest' => $latestRequest,
            'upcomingVisit' => $upcomingVisit,
        ]);
    }
}
