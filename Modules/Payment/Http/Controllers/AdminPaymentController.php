<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Modules\Payment\Http\Requests\PaymentRequest;
use Modules\Payment\Http\Requests\ProcessPaymentRequest;
use Modules\Payment\Services\AdminPaymentService;
use Modules\Payment\Transformers\PaymentResource;

class AdminPaymentController extends Controller
{
    use HttpResponse;

    public function __construct(public AdminPaymentService $adminPaymentService) {}

    public function index()
    {
        $payments = $this->adminPaymentService->index();

        return $this->paginatedResponse($payments, PaymentResource::class);
    }

    public function show($id)
    {
        $payment = $this->adminPaymentService->show($id);

        return $this->successResponse(PaymentResource::make($payment));
    }

    public function process(ProcessPaymentRequest $request)
    {
        $payment = $this->adminPaymentService->processPayment($request->validated());

        return $this->okResponse(
            PaymentResource::make($payment),
            message: 'Payment processed successfully'
        );
    }
}
