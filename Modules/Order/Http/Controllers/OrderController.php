<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Modules\Order\Http\Requests\OrderRequest;
use Modules\Order\Services\OrderService;
use Modules\Order\Transformers\OrderResource;
use Modules\Payment\Transformers\PaymentResource;

class OrderController extends Controller
{
    use HttpResponse;

    public function __construct(public OrderService $OrderService) {}

    public function index()
    {
        $orders = $this->OrderService->index();

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    public function show($id)
    {
        $order = $this->OrderService->show($id);

        return $this->successResponse(OrderResource::make($order));
    }

    public function store(OrderRequest $request)
    {
        $order = $this->OrderService->store($request->validated());

        return $this->okResponse(OrderResource::make($order->load('items')), message: translate_success_message('order', 'created'));
    }

    public function update(OrderRequest $request, $id)
    {
        $order = $this->OrderService->update($id, $request->validated());

        return $this->okResponse(OrderResource::make($order->load('items')), message: translate_success_message('order', 'updated'));
    }

    public function destroy($id)
    {
        $this->OrderService->destroy($id);

        return $this->okResponse(message: translate_success_message('order', 'deleted'));
    }

    public function payments($id)
    {
        $order = $this->OrderService->show($id);
        $payments = $order->payments()->latest()->paginate();

        return $this->paginatedResponse($payments, PaymentResource::class);
    }
}
