<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Models\Request as ModelsRequest;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RequestController extends Controller
{
    public function index()
    {
        $requests = ModelsRequest::query();

        if (request()->has('search')) {
            $requests->where('name', 'like', '%' . request()->input('search') . '%');
        }

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = str_starts_with($attribute, '-') ? 'DESC' : 'ASC';
            $attribute = ltrim($attribute, '-');
            $requests->orderBy($attribute, $sort_order);
        } else {
            $requests->latest();
        }

        if (request()->has('status') && request()->input('status') !== null) {
            $requests->where('status', request()->input('status'));
        }

        $requests = $requests->with('visitors')->paginate(10)->onEachSide(2)->appends(request()->query());

        return Inertia::render('Admin/Request/Index', [
            'requests' => $requests,
            'filters' => request()->all('search', 'status'),
            'can' => [
                'create' => Auth::user()->can('request-create'),
                'edit' => Auth::user()->can('request-edit'),
                'acceptance' => Auth::user()->can('request-acceptance'),
                'reject' => Auth::user()->can('request-reject'),
                'delete' => Auth::user()->can('request-delete'),
            ],
        ]);
    }

    private function storeVisit($request)
    {
        $spkName = $request->hasFile('spk') ? '/file/spk/' . time() . '.' . $request->file('spk')->extension() : null;

        if ($spkName) {
            Storage::putFileAs(env('APP_FDIR') . '/file/spk', $request->file('spk'), time() . '.' . $request->file('spk')->extension());
        }

        $requestData = ModelsRequest::create([
            'visit_purpose' => $request->visit_purpose,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'description' => $request->description,
            'spk' => $spkName ? env('APP_FDIR_URL') . env('APP_FDIR') . $spkName : null
        ]);

        foreach ($request->visitors as $key => $visitor) {
            if (!isset($visitor['file_ktp']) || !$visitor['file_ktp']->isValid()) {
                return redirect()->back()->with('error', 'File KTP wajib diunggah dan harus valid.');
            }

            // Simpan file mentah ke storage
            $fileName = 'ktp_' . uniqid() . '.' . $visitor['file_ktp']->getClientOriginalExtension();
            $filePath = "private/ktp/{$fileName}";
            Storage::disk('local')->putFileAs('private/ktp', $visitor['file_ktp'], $fileName);

            // Enkripsi path sebelum disimpan di database
            $encryptedPath = Crypt::encrypt($filePath);

            $visitorData = Visitor::create([
                'name' => $visitor['name'] ?? 'Unknown',
                'file_ktp' => $encryptedPath,
                'company' => $visitor['company'] ?? null,
                'occupation' => $visitor['occupation'] ?? null,
                'phone' => isset($visitor['phone']) ? '0' . $visitor['phone'] : null,
                'email' => $visitor['email'] ?? null,
                'pic' => $key == 0
            ]);

            $requestData->visitors()->attach($visitorData->id);
        }
    }

    public function formVisitStore(StoreVisitRequest $request)
    {
        $this->storeVisit($request);
        return redirect()->route('form.visit')->with('message', __('Request created successfully.'));
    }

    public function store(StoreVisitRequest $request)
    {
        $this->storeVisit($request);
        return redirect()->route('request.index')->with('message', __('Request created successfully.'));
    }

public function acceptance($id, $action)
{
    $modelsRequest = ModelsRequest::findOrFail($id);

    $validActions = [
        'accept' => 'accepted',
        'reject' => 'rejected',
        'finished' => 'finished',
        'missed' => 'missed',
    ];

    if (!array_key_exists($action, $validActions)) {
        return redirect()->back()->with([
            'message' => 'Invalid action.',
            'color' => 'danger',
        ]);
    }

    $modelsRequest->status = $validActions[$action];
    $modelsRequest->save();

    return redirect()->back()->with([
        'message' => 'Request has been ' . $validActions[$action] . '.',
        'color' => match ($action) {
            'accept' => 'success',
            'reject' => 'danger',
            'finished' => 'success',
            'missed' => 'warning',
            default => 'info',
        },
    ]);
}

    public function showKtp($id)
    {
        $visitor = Visitor::findOrFail($id);

        try {
            $filePath = Crypt::decrypt($visitor->file_ktp);
        } catch (\Exception $e) {
            abort(403, 'Path file tidak valid atau rusak.');
        }

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File KTP tidak ditemukan.');
        }

        Log::info('Admin mengakses file KTP.', [
            'admin_id' => auth()->id(),
            'visitor_id' => $visitor->id,
        ]);

        return response()->file(storage_path('app/' . $filePath));
    }
}
