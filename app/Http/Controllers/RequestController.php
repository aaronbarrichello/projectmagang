<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Jobs\AcceptanceJob;
use App\Mail\AcceptanceMail;
use App\Models\Request as ModelsRequest;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = (new ModelsRequest)->newQuery();

        if (request()->has('search')) {
            $requests->where('name', 'Like', '%' . request()->input('search') . '%');
        }

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $requests->orderBy($attribute, $sort_order);
        } else {
            $requests->latest();
        }

        if (request()->has('status') && request()->input('status') !== null) {
            $requests->where('status', request()->input('status'));
        }

        $requests = $requests
            ->with('visitors')
            // ->where('status', '!=', 'finished')
            ->paginate(10)
            ->onEachSide(2)
            ->appends(request()->query());
        // dd($requests[0]->visitors);
        return Inertia::render('Admin/Request/Index', [
            'requests' => $requests,
            'filters' => request()->all('search', 'status'),
            'can' => [
                'create' => Auth::user()->can('request-create'),
                'edit' => Auth::user()->can('request-edit'),
                'acceptance' => Auth::user()->can('request-acceptance'),
                'delete' => Auth::user()->can('request-delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // dd(Storage::disk('local')->get('spk/1711863172.pdf'));
        return Inertia::render('Admin/Request/Create');
    }

    private function storeVisit($request)
    {
        $spkName = null;
        if ($request->hasFile('spk')) {
            $spkName = '/file/spk/' . time() . '.' . $request['spk']->extension();
            Storage::disk('public')->putFileAs('/file/spk/', $request['spk'], time() . '.' . $request['spk']->extension());
        }

        $requestData = ModelsRequest::create([
            'visit_purpose' => $request->visit_purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'spk' => $spkName
        ]);
        foreach ($request->visitors as $key => $visitor) {
            $ktpName = '/img/ktp/' . time() . '.' . $visitor['file_ktp']->extension();
            Storage::disk('public')->putFileAs('/img/ktp/', $visitor['file_ktp'], time() . '.' . $visitor['file_ktp']->extension());

            $visitorData = Visitor::create([
                'ktp' => $visitor['ktp'],
                'name' => $visitor['name'],
                'file_ktp' => $ktpName,
                'company' => $visitor['company'],
                'occupation' => $visitor['occupation'],
                'phone' => '0'.$visitor['phone'],
                'email' => $visitor['email'],
                'pic' => $key == 0
            ]);

            $requestData->visitors()->attach($visitorData->id);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisitRequest $request)
    {
        $this->storeVisit($request);

        return redirect()->route('request.index')->with('message', __('Request created successfully.'));
    }

    public function formVisitStore(StoreVisitRequest $request)
    {
        $this->storeVisit($request);

        return redirect()->route('form.visit')->with('message', __('Request created successfully.'));
    }

    public function acceptance(Request $request)
    {
        $requestData = ModelsRequest::find($request->id);
        $startDate = Carbon::parse($requestData->start_date)->timezone('UTC');
        $endDate = Carbon::parse($requestData->end_date)->timezone('UTC');
        $requestThisDay = ModelsRequest::where('status', 'accepted')
            ->whereDate('start_date', $startDate->toDateString())
            ->whereTime('end_date', '>=', $startDate->toTimeString())
            ->whereTime('end_date', '<=', $endDate->toTimeString())
            ->whereTime('start_date', '<=', $endDate->toTimeString())
            ->whereTime('start_date', '>=', $startDate->toTimeString())
            ->count();
        $filename = '';
        $action = $request->action . 'ed';

        if ($action == 'accepted') {
            if ($requestThisDay > 0) {
                return redirect()->route('request.index')
                    ->with('message', __('Request Bentrok.'))
                    ->with('color', 'warning');
            }

            $qrCode = QrCode::format('png')->size(256)->generate($requestData->uuid);
            $filename = '/img/qr-code/img-' . time() . '.png';
            Storage::disk('public')->put($filename, $qrCode);
            $requestData->update([
                'qrcode' => $filename
            ]);
        }

        $requestData->update([
            'status' => $action
        ]);

        $pic = $requestData->visitors()->where('pic', true)->first();

        AcceptanceJob::dispatch($pic->email, $action, $filename);

        return redirect()->route('request.index')->with('message', __('Request ' . $action . ' successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requestData = ModelsRequest::with(['visitors' => function ($query) {
            $query->orderBy('pic', 'desc');
        }])->findOrFail($id);

        return Inertia::render('Admin/Request/Show', [
            'data' => $requestData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $string)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $string)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $string)
    {
        //
    }
}
