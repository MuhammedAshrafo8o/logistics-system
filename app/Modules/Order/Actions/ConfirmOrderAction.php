<?php

namespace App\Modules\Order\Actions;

use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Models\Order;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConfirmOrderAction
{
    public function execute(Order $order): Order
    {
        if ($order->status === OrderStatus::CONFIRMED) {
            $this->fail('Order is already confirmed.');
        }

        if ($order->status === OrderStatus::CANCELLED) {
            $this->fail('Cancelled order cannot be confirmed.');
        }

        if ($order->requires_review) {
            $this->fail('Order requires review before confirmation.');
        }

        if (! in_array($order->status, [OrderStatus::DRAFT, OrderStatus::PENDING_REVIEW], true)) {
            $this->fail('Only draft or pending review orders can be confirmed.');
        }

        if (! $order->items()->exists()) {
            $this->fail('Order must have at least one item before confirmation.');
        }

        $order->update([
            'status' => OrderStatus::CONFIRMED,
        ]);

        return $order->fresh();
    }

    private function fail(string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => $message,
        ], 422));
    }
}
