<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlaggedCustomerReason extends Model
{
    protected $fillable = [
        'customer_id',
        'reason',
        'action_by',
        'action_type',
    ];
     // Optional: cast if needed
    protected $casts = [
        'customer_id' => 'integer',
        'action_by' => 'integer',
    ];
    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
