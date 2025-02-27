<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BusinessSetting extends Model
{
    public function translation(): MorphMany
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }

    protected $fillable = [
        'key',
        'value'
    ];
}
