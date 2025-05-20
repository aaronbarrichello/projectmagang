<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ScanController extends Controller
{
    /**
     * Tampilkan halaman scan QR code.
     */
    public function index()
    {
        return Inertia::render('Scan/Index');
    }
    
    /**
     * Verifikasi QR code.
     */
    public function verify($uuid)
    {
        $request = ModelsRequest::where('uuid', $uuid)->first();
        
        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak ditemukan.'
            ]);
        }
        
        // Periksa apakah request valid untuk kunjungan
        if (!$request->isValidForVisit()) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan tidak valid. Periksa status atau waktu kunjungan.'
            ]);
        }
        
        // Jika valid, kembalikan data kunjungan
        return response()->json([
            'success' => true,
            'message' => 'QR Code valid. Kunjungan diizinkan.',
            'data' => [
                'visit_purpose' => $request->visit_purpose,
                'start_date' => $request->start_date->format('d M Y H:i'),
                'end_date' => $request->end_date->format('d M Y H:i'),
                'visitors' => $request->visitors->map(function ($visitor) {
                    return [
                        'name' => $visitor->name,
                        'company' => $visitor->company,
                        'is_pic' => $visitor->pic
                    ];
                })
            ]
        ]);
    }
    
    /**
     * Proses check-in pengunjung.
     */
    public function checkIn(Request $request)
    {
        $uuid = $request->input('uuid');
        
        $visitRequest = ModelsRequest::where('uuid', $uuid)->first();
        
        if (!$visitRequest) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak ditemukan.'
            ]);
        }
        
        // Periksa apakah request valid untuk kunjungan
        if (!$visitRequest->isValidForVisit()) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan tidak valid. Periksa status atau waktu kunjungan.'
            ]);
        }
        
        // Catat waktu check-in (bisa ditambahkan ke model atau tabel terpisah)
        // Contoh: $visitRequest->check_in_time = Carbon::now();
        // $visitRequest->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil.',
            'data' => [
                'visit_id' => $visitRequest->id,
                'check_in_time' => Carbon::now()->format('d M Y H:i:s')
            ]
        ]);
    }
}