<?php

namespace Modules\Payment\Models\Scopes;

use Modules\Payment\Enum\PaymentStatusEnum;

trait PaymentScope
{
    public function scopeFilterByOrder()
    {
        if (request()->filled('order_id')) {
            $this->where('order_id', request('order_id'));
        }
    }

    public function filterByOrder()
    {
        $this->scopeFilterByOrder();

        return $this;
    }

    public function scopeFilterByStatus()
    {
        $status = request('status');
        if (! is_null($status) && $status !== '') {
            $parsed = PaymentStatusEnum::parseStatus($status);
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
