<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderDashboardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name ?? 'Guest',
            'customer_phone' => $this->customer_phone ?? '—',
            'total_price' => number_format((float) $this->total_price, 2),
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_at_formatted' => $this->created_at?->format('M d, Y g:i A'),
            'items' => OrderItemDashboardResource::collection($this->whenLoaded('items')),
        ];
    }
}
