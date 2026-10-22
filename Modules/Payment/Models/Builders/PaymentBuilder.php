<?php

namespace Modules\Payment\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Payment\Models\Scopes\PaymentScope;

class PaymentBuilder extends Builder
{
    use PaymentScope;

    public function adminDetails()
    {
        return $this->select([
            'id',
            'order_id',
            'status',
            'method',
            'amount',
            'transaction_id',
            'created_at',
        ])->order();
    }

    public function order()
    {
        return $this->with('order');
    }
}
