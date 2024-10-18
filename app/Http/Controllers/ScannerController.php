<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScannerController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Scanner/Index');
    }

    public function checkQrCode(Request $request)
    {
        $requestData = ModelsRequest::where('uuid', $request->id)->whereDate('start_date', Carbon::now()->toDateString())->first();

        if (!$requestData) {
            return response()->json(['message' => 'data tidak ada...'], 500);
        }

        $requestStartDate = Carbon::parse($requestData->start_date);
        $diffStartDate = $requestStartDate->diffInHours(Carbon::now());

        if ($diffStartDate < -3) {
            return response()->json(['message' => 'belum waktunya...'], 500);
        }

        $requestEndDate = Carbon::parse(($requestData->end_date));
        $diffEndDate = $requestEndDate->diffInHours(Carbon::now());

        if ($diffEndDate > 0) {
            return response()->json(['message' => 'udah lewat...'], 500);
        }

        if ($requestData->status == 'accepted') {
            $requestData->update([
                'status' => 'finished'
            ]);
        }

        return response()->json(['message' => 'Silahkan Masuk']);
    }
}
