<?php

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;

class GenerateOrderNumberAction
{
    public function execute(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'ORD-'.$datePart.'-';

        $latestOrderNumber = Order::withTrashed()
            ->where('order_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('order_number')
            ->value('order_number');

        $nextSequence = $latestOrderNumber === null
            ? 1
            : ((int) substr($latestOrderNumber, -6)) + 1;

        return $prefix.str_pad((string) $nextSequence, 6, '0', STR_PAD_LEFT);
    }
}
