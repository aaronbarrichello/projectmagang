<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request as ModelsRequest;
use Carbon\Carbon;

class MissedRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missed:requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change expired requests status to missed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Cron Job running at ". now());

        $expiredRequests = ModelsRequest::whereDate('start_date', '<', Carbon::now()->toDateString())
            ->where('status', 'requested')
            // ->orWhere('status', 'accepted')
            ->get();

        if ($expiredRequests->count() > 0) {
            foreach ($expiredRequests as $request) {
                $request->update([
                    'status' => 'missed'
                ]);
            }
        }
    }
}
