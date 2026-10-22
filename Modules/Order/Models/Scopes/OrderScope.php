<?php

namespace Modules\Order\Models\Scopes;

use Modules\Order\Enum\OrderStatusEnum;

trait OrderScope
{
    public function scopeFilterByStatus()
    {
        $status = request('status');
        if (! is_null($status) && $status !== '') {
            $parsed = OrderStatusEnum::parseStatus($status);
            if ($parsed !== null) {
                $this->where('status', $parsed);
            }
        }
    }

    public function filterByStatus()
    {
        $this->scopeFilterByStatus();

        return $this;
    }
}
