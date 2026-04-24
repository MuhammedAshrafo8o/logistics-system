<?php

namespace App\Modules\Finance\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantInvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'merchant_name' => $this->whenLoaded('merchant', fn () => $this->merchant?->name),
            'invoice_number' => $this->invoice_number,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'total_cod' => $this->total_cod,
            'total_shipping_fees' => $this->total_shipping_fees,
            'total_warehouse_charges' => $this->total_warehouse_charges,
            'total_payable' => $this->total_payable,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_by_name' => $this->whenLoaded('createdBy', fn () => $this->createdBy?->name),
            'issued_at' => $this->issued_at,
            'file_path' => $this->file_path,
            'generated_at' => $this->generated_at,
            'download_count' => $this->download_count,
            'last_downloaded_at' => $this->last_downloaded_at,
            'created_at' => $this->created_at,
        ];
    }
}
