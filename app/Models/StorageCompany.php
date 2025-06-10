<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StorageCompany extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'verified',
    ];

    public function storageFacilities(): HasMany
    {
        return $this->hasMany(StorageFacility::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
