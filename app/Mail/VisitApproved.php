<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Request as ModelsRequest;
use App\Models\Visitor;

class VisitApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $visitor;
    public $qrCodeUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModelsRequest $request, Visitor $visitor, $qrCodeUrl)
    {
        $this->request = $request;
        $this->visitor = $visitor;
        $this->qrCodeUrl = $qrCodeUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Kunjungan Anda Telah Disetujui')
                    ->markdown('emails.visit-approved')
                    ->with([
                        'request' => $this->request,
                        'visitor' => $this->visitor,
                        'qrCodeUrl' => $this->qrCodeUrl,
                        'startDate' => $this->request->start_date,
                        'endDate' => $this->request->end_date
                    ]);
    }
}