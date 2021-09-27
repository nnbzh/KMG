<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }
}
