<?php

namespace App\Modules\Warehouse\Requests;

use App\Modules\Order\Models\Order;
use App\Modules\Warehouse\Enums\WarehouseStatus;
use App\Modules\Warehouse\Models\Warehouse;
use App\Modules\Warehouse\Models\WarehouseProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReserveStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => [
                'required',
                Rule::exists('warehouses', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => [
                'required',
                'distinct',
                Rule::exists('order_items', 'id'),
            ],
            'items.*.warehouse_product_id' => [
                'required',
                Rule::exists('warehouse_products', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Order|null $order */
            $order = $this->route('order');

            if (! $order instanceof Order) {
                return;
            }

            /** @var Warehouse|null $warehouse */
            $warehouse = Warehouse::query()->find($this->integer('warehouse_id'));

            if ($warehouse !== null && $warehouse->status !== null && $warehouse->status !== WarehouseStatus::ACTIVE) {
                $validator->errors()->add('warehouse_id', 'The selected warehouse must be active.');
            }

            $items = $this->input('items', []);
            $orderItemIds = collect($items)->pluck('order_item_id')->filter()->unique()->values();
            $warehouseProductIds = collect($items)->pluck('warehouse_product_id')->filter()->unique()->values();

            $matchingOrderItemsCount = $order->items()
                ->whereIn('id', $orderItemIds)
                ->count();

            if ($matchingOrderItemsCount !== $orderItemIds->count()) {
                $validator->errors()->add('items', 'Every order item must belong to the selected order.');
            }

            $matchingProductsCount = WarehouseProduct::query()
                ->whereIn('id', $warehouseProductIds)
                ->where('merchant_id', $order->merchant_id)
                ->count();

            if ($matchingProductsCount !== $warehouseProductIds->count()) {
                $validator->errors()->add('items', 'Every warehouse product must belong to the same merchant as the order.');
            }
        });
    }
}
