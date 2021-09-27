<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Indicator extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = ['type_id', 'qliq', 'qoil', 'date', 'company_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function type(): HasOne
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }
}
