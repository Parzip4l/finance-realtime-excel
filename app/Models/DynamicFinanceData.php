<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicFinanceData extends Model
{
    protected $fillable = ['type', 'meta', 'data'];

    protected $casts = [
        'meta' => 'array',
        'data' => 'array',
    ];
}
