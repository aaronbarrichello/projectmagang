@component('mail::message')
# Kunjungan Anda Telah Disetujui

Halo {{ $visitor->name }},

Permintaan kunjungan Anda telah disetujui dengan detail sebagai berikut:

**Tujuan Kunjungan:** {{ $request->visit_purpose }}  
**Tanggal Mulai:** {{ $startDate }}  
**Tanggal Selesai:** {{ $endDate }}  
**Deskripsi:** {{ $request->description }}  

Silakan tunjukkan QR Code berikut saat Anda tiba:

<div style="text-align: center; margin: 30px 0;">
    <img src="{{ $qrCodeUrl }}" alt="QR Code" style="width: 250px; height: 250px;">
</div>

<div style="text-align: center; margin: 20px 0; font-size: 12px; color: #666;">
    Jika QR code tidak muncul, silakan klik link berikut:
    <a href="{{ $qrCodeUrl }}" target="_blank">Lihat QR Code</a>
</div>

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent