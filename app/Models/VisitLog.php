<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'type', // check_in, check_out
        'user_id',
        'notes'
    ];

    /**
     * Get the request that owns the log.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}