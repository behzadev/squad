<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'count_init',
        'count_left',
    ];

    public function isUsable(): bool
    {
        return $this->count_left > 0;
    }
}
