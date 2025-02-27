<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    public bool $timestamps = false;

    protected $fillable =[
        'transalationable_type',
        'transalationable_id',
        'locale',
        'key',
        'value'
    ];

    public function transalationable(): MorphTo
    {
        return $this->morphTo();
    }
}
