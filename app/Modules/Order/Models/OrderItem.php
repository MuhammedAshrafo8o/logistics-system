<?php

namespace App\Modules\Order\Models;

use App\Modules\Warehouse\Models\OrderStockReservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price',
        'weight',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function stockReservations(): HasMany
    {
        return $this->hasMany(OrderStockReservation::class);
    }
}
