<?php

namespace App\Modules\Shipment\Models;

use App\Models\Merchant;
use App\Models\User;
use App\Modules\Driver\Models\Driver;
use App\Modules\LocationPricing\Models\Area;
use App\Modules\LocationPricing\Models\Governorate;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'shipment_number',
        'merchant_id',
        'customer_name',
        'customer_phone',
        'delivery_governorate_id',
        'delivery_area_id',
        'delivery_address',
        'cod_amount',
        'shipping_fee',
        'status',
        'tracking_notes',
        'created_by',
        'assigned_driver_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cod_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function deliveryGovernorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'delivery_governorate_id');
    }

    public function deliveryArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'delivery_area_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'assigned_driver_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class)->orderBy('id');
    }
}
