<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorageFacility extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'storage_company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(StorageCompany::class, 'storage_company_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
