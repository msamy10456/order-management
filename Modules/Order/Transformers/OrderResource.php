<?php

namespace Modules\Order\Transformers;

use App\Helpers\ResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Transformers\UserResource;
use Modules\Order\Enum\OrderMediaEnum;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'customer_name' => $this->whenHas('customer_name'),
            'customer_email' => $this->whenHas('customer_email'),
            'customer_phone' => $this->whenHas('customer_phone'),
            'address' => $this->whenHas('address'),
            'status' => $this->whenHas('status'),
            'total_items' => (float) ($this->items_sum_total ?? ($this->relationLoaded('items') ? $this->items->sum('total') : 0)),
            'total_price' => (float) ($this->items_sum_total ?? ($this->relationLoaded('items') ? $this->items->sum('total') : 0)),
            'count_items' => (int) ($this->items_sum_quantity ?? ($this->relationLoaded('items') ? $this->items->sum('quantity') : 0)),
            'created_at' => $this->whenHas('created_at'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
