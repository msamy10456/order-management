<?php

namespace Modules\Order\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'order_id' => $this->whenHas('order_id'),
            'product_name' => $this->whenHas('product_name'),
            'quantity' => $this->whenHas('quantity'),
            'price' => $this->whenHas('price'),
            'total' => $this->whenHas('total'),
        ];
    }
}
