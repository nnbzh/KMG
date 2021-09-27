<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $fillable = ['slug', 'name'];

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }
}
