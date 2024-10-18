<?php

namespace App\Http\Controllers;

use App\Exports\RequestExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Request as ModelsRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use Maatwebsite\Excel\Facades\Excel;

class AnalyticController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        $now = Carbon::now();
        if (request()->has('tren') && request()->input('tren') !== null) {
            $filterTren = request()->input('tren');
        } else {
            $filterTren = ['month' => (int) $now->month - 1, 'year' => $now->year];
        }

        $dataPerMonth = ModelsRequest::whereYear('start_date', $filterTren['year'])
            ->whereMonth('start_date', $filterTren['month'] + 1)
            ->with('visitors')
            ->paginate(10);

        $dataByPurpose = ModelsRequest::select(DB::raw('COUNT(*) as visit_count'), DB::raw('visit_purpose'))
            ->whereYear('start_date', $filterTren['year'])
            ->whereMonth('start_date', $filterTren['month'] + 1)
            ->groupBy('visit_purpose')
            ->get()->toArray();

        return Inertia::render('Admin/Analytic/Index', [
            'dataPerMonth' => $dataPerMonth,
            'dailyData' => $this->getRequestDataByDate($filterTren),
            'dataByPurpose' => $dataByPurpose,
            'filters' => [
                'tren' => $filterTren
            ],
        ]);
    }

    public function getRequestDataByDate($filter)
    {
        $filter['month'] += 1;

        $start = Carbon::createFromDate(null, $filter['month'])->startOfMonth();
        $end = Carbon::createFromDate(null, $filter['month'])->endOfMonth();
        $dates = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = (object) [
                'date' => $date->format('d'),
                'totalRequest' => 0
            ];
        }

        $requestData = ModelsRequest::whereMonth('start_date', '=', $filter['month'])
            ->whereDay('start_date', '>=', $start)
            ->whereDay('start_date', '<=', $end)
            ->get();

        foreach ($requestData as $request) {
            $date = Carbon::parse($request->start_date)->format('d');
            $index = array_search($date, array_column($dates, 'date'));

            if ($index !== false) {
                $dates[$index]->totalRequest++;
            }
        }

        return $dates;
    }

    public function export(String $year, String $month)
    {
        $monthPlusOne = (int) $month + 1;
        $excelName = 'report-' . $year . '-' . $monthPlusOne . '.xlsx';
        return (new RequestExport($year, $monthPlusOne))->download($excelName);
    }
}
