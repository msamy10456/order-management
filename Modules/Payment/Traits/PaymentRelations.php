<?php

namespace Modules\Payment\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\Models\Order;

trait PaymentRelations
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
