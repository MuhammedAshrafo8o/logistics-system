<?php

namespace App\Modules\Order\Controllers;

use App\Modules\Order\Actions\ConfirmOrderAction;
use App\Modules\Order\Actions\GenerateOrderNumberAction;
use App\Modules\Order\Enums\OrderFulfillmentType;
use App\Modules\Order\Enums\OrderSource;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentType;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Requests\ListOrdersRequest;
use App\Modules\Order\Requests\StoreOrderRequest;
use App\Modules\Order\Requests\UpdateOrderRequest;
use App\Modules\Order\Resources\OrderResource;
use App\Modules\Order\Services\OrderShippingFeeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderController
{
    public function __construct(
        private readonly ConfirmOrderAction $confirmOrderAction,
        private readonly GenerateOrderNumberAction $generateOrderNumberAction,
        private readonly OrderShippingFeeService $orderShippingFeeService,
    ) {
    }

    public function index(ListOrdersRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $orders = Order::query()
            ->with($this->relations())
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->when(isset($validated['merchant_id']), fn ($query) => $query->where('merchant_id', $validated['merchant_id']))
            ->when(isset($validated['merchant_name']), fn ($query) => $query->whereHas('merchant', function ($merchantQuery) use ($validated) {
                $merchantQuery->where('name', 'like', '%'.$validated['merchant_name'].'%');
            }))
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['fulfillment_type']), fn ($query) => $query->where('fulfillment_type', $validated['fulfillment_type']))
            ->when(isset($validated['payment_type']), fn ($query) => $query->where('payment_type', $validated['payment_type']))
            ->when(isset($validated['search']), function ($query) use ($validated) {
                $search = $validated['search'];

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('order_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('customer_phone', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $orderData = $this->buildOrderAttributes($request, $validated, true);
            $orderData['order_number'] = $this->generateOrderNumberAction->execute();
            $orderData['created_by'] = $request->user()?->id;

            $this->applyShippingFeeOnCreate($request, $orderData);

            $order = Order::create($orderData);
            $order->items()->createMany($this->buildItemsPayload($validated['items']));

            return $order->load($this->relations());
        });

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load($this->relations()));
    }

    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->ensureOrderCanBeModified($order, 'Order cannot be edited after shipment has been created.');

        $order = DB::transaction(function () use ($request, $order) {
            $validated = $request->validated();
            $orderData = $this->buildOrderAttributes($request, $validated);

            $order->update($orderData);

            if (Arr::exists($validated, 'items')) {
                $order->items()->delete();
                $order->items()->createMany($this->buildItemsPayload($validated['items']));
            }

            return $order->fresh()->load($this->relations());
        });

        return new OrderResource($order);
    }

    public function confirm(Order $order): OrderResource
    {
        $order = $this->confirmOrderAction->execute($order);

        return new OrderResource($order->load($this->relations()));
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->ensureOrderCanBeModified($order, 'Order cannot be deleted after shipment has been created.');

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildOrderAttributes(Request $request, array $validated, bool $applyDefaults = false): array
    {
        $attributes = Arr::only($validated, [
            'merchant_id',
            'customer_name',
            'customer_phone',
            'customer_phone_alt',
            'delivery_governorate_id',
            'delivery_area_id',
            'delivery_address',
            'delivery_notes',
            'pickup_governorate_id',
            'pickup_area_id',
            'pickup_address',
            'pickup_notes',
            'cod_amount',
            'shipping_fee',
            'payment_type',
            'fulfillment_type',
            'is_fragile',
            'allow_inspection',
            'requires_packaging',
            'package_notes',
            'source',
            'external_source',
            'external_order_id',
            'external_order_number',
            'requires_review',
            'review_reason',
            'status',
            'notes',
        ]);

        foreach (['is_fragile', 'allow_inspection', 'requires_packaging', 'requires_review'] as $field) {
            if ($request->has($field)) {
                $attributes[$field] = $request->boolean($field);
            }
        }

        if ($applyDefaults && ! Arr::exists($attributes, 'fulfillment_type')) {
            $attributes['fulfillment_type'] = OrderFulfillmentType::PICKUP_FROM_MERCHANT;
        }

        if ($applyDefaults && ! Arr::exists($attributes, 'source')) {
            $attributes['source'] = OrderSource::MANUAL;
        }

        if ($applyDefaults && ! Arr::exists($attributes, 'status')) {
            $attributes['status'] = OrderStatus::DRAFT;
        }

        if ($applyDefaults && ! Arr::exists($attributes, 'payment_type')) {
            $attributes['payment_type'] = PaymentType::COD;
        }

        if ($applyDefaults && (! Arr::exists($attributes, 'cod_amount') || $attributes['cod_amount'] === null)) {
            $attributes['cod_amount'] = '0.00';
        }

        if (
            $applyDefaults
            && ($attributes['payment_type'] ?? PaymentType::COD) === PaymentType::PREPAID
            && (! Arr::exists($attributes, 'cod_amount') || $attributes['cod_amount'] === null)
        ) {
            $attributes['cod_amount'] = '0.00';
        }

        if (Arr::exists($attributes, 'shipping_fee') && $attributes['shipping_fee'] === null) {
            unset($attributes['shipping_fee']);
        }

        return $attributes;
    }

    /**
     * @param array<string, mixed> $orderData
     */
    private function applyShippingFeeOnCreate(Request $request, array &$orderData): void
    {
        if ($request->filled('shipping_fee')) {
            return;
        }

        $resolvedShipping = $this->orderShippingFeeService->resolve(
            (int) $orderData['delivery_governorate_id'],
            isset($orderData['delivery_area_id']) ? (int) $orderData['delivery_area_id'] : null,
        );

        $orderData['shipping_fee'] = $resolvedShipping['shipping_fee'];

        if ($resolvedShipping['source'] !== null) {
            return;
        }

        $orderData['requires_review'] = true;
        $orderData['review_reason'] = 'Shipping rate not found for delivery location';
        $orderData['status'] = OrderStatus::PENDING_REVIEW;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function buildItemsPayload(array $items): array
    {
        return array_map(function (array $item): array {
            return [
                'product_name' => $item['product_name'],
                'sku' => $item['sku'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? '0.00',
                'weight' => $item['weight'] ?? null,
                'notes' => $item['notes'] ?? null,
            ];
        }, $items);
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'merchant',
            'deliveryGovernorate',
            'deliveryArea',
            'shipment',
            'latestStockReservation',
            'items',
        ];
    }

    private function ensureOrderCanBeModified(Order $order, string $message): void
    {
        if ($order->shipment()->exists()) {
            throw new HttpResponseException(response()->json([
                'message' => $message,
            ], 422));
        }
    }
}
