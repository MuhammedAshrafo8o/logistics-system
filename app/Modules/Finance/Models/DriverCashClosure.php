<?php

namespace App\Modules\Finance\Models;

use App\Models\User;
use App\Modules\Driver\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverCashClosure extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'driver_id',
        'closure_date',
        'expected_amount',
        'received_amount',
        'difference_amount',
        'status',
        'notes',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'closure_date' => 'date',
            'expected_amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'verified_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
