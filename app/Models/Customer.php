<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'rsa_id',
        'user_id',
        'is_flagged', 
        'flagged_reason',
    ];
    protected $casts = ['is_flagged' => 'boolean'];

    // public function facility(): BelongsTo
    // {
    //     return $this->belongsTo(StorageFacility::class, 'storage_facility_id');
    // }

    public function flaggedReasons(): HasMany
    {
        return $this->hasMany(FlaggedCustomerReason::class);
    }
    public function getIsFlaggedAttribute(): bool
    {
        return $this->flaggedReasons()->exists();
    }
    public function getFlaggedReasonAttribute(): ?string
    {
        return $this->flaggedReasons()->latest()->first()?->reason;
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    // public function belongsToCurrentUser(): bool
    // {
    //     $user = auth()->user();

    //     if (! $user) {
    //         return false;
    //     }

    //     return $this->facility->storage_company_id === $user->storage_company_id;
    // }



}
