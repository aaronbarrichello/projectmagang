<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Visitor extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'uuid',
        'ktp',
        'name',
        'file_ktp',
        'file_nda',
        'company',
        'occupation',
        'phone',
        'email',
        'pic'
    ];

    public function requests(): BelongsToMany
    {
        return $this->belongsToMany(Request::class);
    }
}
