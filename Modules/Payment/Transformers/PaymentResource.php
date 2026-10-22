<?php

namespace Modules\Payment\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Transformers\OrderResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'order_id' => $this->whenHas('order_id'),
            'status' => $this->whenHas('status'),
            'method' => $this->whenHas('method'),
            'amount' => $this->whenHas('amount'),
            'transaction_id' => $this->whenHas('transaction_id'),
            'created_at' => $this->whenHas('created_at'),
            'order' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
