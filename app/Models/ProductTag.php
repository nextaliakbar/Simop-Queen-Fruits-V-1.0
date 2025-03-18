<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $table = 'product_tag';

    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'tag_id' => 'integer'
    ];
}
