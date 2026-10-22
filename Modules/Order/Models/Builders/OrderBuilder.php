<?php

namespace Modules\Order\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Order\Models\Scopes\OrderScope;

class OrderBuilder extends Builder
{
    use OrderScope;
    public function indexDetails()
    {
        return $this->select([
            'id',
            'user_id',
            'customer_name',
            'customer_email',
            'customer_phone',
            'address',
            'status',
            'created_at',
        ])->withTotalPriceItems()->withCountItems();
    }
    public function showDetails()
    {
        return $this->select([
            'id',
            'user_id',
            'customer_name',
            'customer_email',
            'customer_phone',
            'address',
            'status',
            'created_at',
        ])->withTotalPriceItems()->withCountItems()->user()->items();
    }

    public function user()
    {
        return $this->with('user:id,name,email');
    }

    public function withTotalPriceItems()
    {
        return $this->withSum('items', 'total');
    }
    public function withCountItems()
    {
        return $this->withSum('items', 'quantity');
    }

    public function items()
    {
        return $this->with('items');
    }
}
