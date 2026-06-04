<?php

namespace App\Http\Resources;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItem */
class OrderItemDashboardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lineTotal = (float) $this->price_at_purchase * (int) $this->quantity;

        return [
            'id' => $this->id,
            'product_name' => $this->product?->name ?? 'Unknown Product',
            'price_at_purchase' => number_format((float) $this->price_at_purchase, 2),
            'quantity' => $this->quantity,
            'line_total' => number_format($lineTotal, 2),
        ];
    }
}
