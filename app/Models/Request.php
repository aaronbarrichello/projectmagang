<?php

namespace App\Models;

use App\Traits\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Request extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'uuid',
        'start_date',
        'end_date',
        'visit_purpose',
        'description',
        'qrcode',
        'status',
        'spk'
    ];

    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class);
    }

    protected function startDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->setTimezone(config('app.timezone'))->format("j F Y H:i"),
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->setTimezone(config('app.timezone'))->format("j F Y H:i"),
        );
    }

    protected function visitPurpose(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            // set: fn (string $value) => strtolower($value),
        );
    }
}
