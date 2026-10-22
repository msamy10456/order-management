<?php

namespace Modules\Payment\Models;

use App\Traits\PaginationTrait;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Modules\Payment\Models\Builders\PaymentBuilder;
use Modules\Payment\Traits\PaymentRelations;

class Payment extends Model
{
    use PaymentRelations,
        PaginationTrait,
        Searchable;

    protected $fillable = [
        'order_id',
        'status',
        'method',
        'amount',
        'transaction_id',
    ];

    public function newEloquentBuilder($query): PaymentBuilder
    {
        return new PaymentBuilder($query);
    }
}
