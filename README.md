# Extendable Order and Payment Management API

A clean, modular Laravel API built for managing orders and payments with an extendable **Payment Gateway Strategy Pattern**.

---

## 📋 Table of Contents
- [Architecture & Design Principles](#architecture--design-principles)
- [Requirements](#requirements)
- [Setup & Installation Instructions](#setup--installation-instructions)
- [Authentication](#authentication)
- [Business Rules](#business-rules)
- [Payment Gateway Extensibility (Strategy Pattern)](#payment-gateway-extensibility-strategy-pattern)
  - [How to Add a New Payment Gateway](#how-to-add-a-new-payment-gateway-in-2-steps)
- [API Endpoints Reference](#api-endpoints-reference)
- [Postman Collection](#postman-collection)
- [Running Automated Tests](#running-automated-tests)

---

## 🏛️ Architecture & Design Principles

The application is structured following modular architecture principles (`Modules/Order` and `Modules/Payment`) and SOLID standards:
- **Single Responsibility Principle (SRP):** Controllers delegate business logic to dedicated services (`OrderService`, `AdminPaymentService`), and gateways handle only their payment processing.
- **Open/Closed Principle (OCP):** New payment gateways can be introduced without modifying existing payment processing code or controllers.
- **Strategy Pattern:** The payment engine relies on `PaymentGatewayInterface` and a `PaymentGatewayManager` to dynamically resolve concrete gateways (`PaypalGateway`, `CreditCardGateway`, `PaymobGateway`).
- **RESTful Principles:** Uniform resource naming, appropriate HTTP verbs (`GET`, `POST`, `PUT`, `DELETE`), consistent status codes (`200`, `201`, `422`, `404`), and standard JSON response envelopes.

---

## ⚙️ Requirements
- PHP `>= 8.2`
- Composer `>= 2.0`
- SQLite or MySQL
- Extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`

---

## 🚀 Setup & Installation Instructions

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd task
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

5. **(Optional) Seed roles and default permissions:**
   ```bash
   php artisan db:seed
   ```

6. **Start local development server:**
   ```bash
   php artisan serve
   ```
   The API will be available at `http://127.0.0.1:8000`.

---

## 🔐 Authentication

API endpoints are secured using **Bearer Token Authentication** (Laravel Sanctum).
- **Register User:** `POST /api/register/user`
- **Login User:** `POST /api/login/mobile`
  - Returns a token in response: `data.token`.
  - Pass this token in the `Authorization` header for protected endpoints:
    ```http
    Authorization: Bearer <your_token>
    ```

---

## 📌 Business Rules

1. **Order Deletion Guard:**
   - An order can only be deleted if it has **no associated payment records**.
   - If an order has associated payments, `DELETE /api/orders/{id}` fails with HTTP **422 Unprocessable Entity** and a clear error message.

2. **Confirmed Order Payment Guard:**
   - Payments can **only be processed for orders in the `confirmed` status**.
   - Attempting to process payment for an order in `pending` or `cancelled` status returns HTTP **422 Unprocessable Entity**.

3. **Automatic Total Calculation:**
   - When orders and items are created, each item's total (`quantity * price`) is calculated, and the aggregate order total is automatically computed.

---

## 💳 Payment Gateway Extensibility (Strategy Pattern)

Payment gateways are decoupled from the payment service using the **Strategy Pattern**.

```
                         +------------------------------+
                         |    PaymentGatewayManager     |
                         +------------------------------+
                                        |
                 +----------------------+----------------------+
                 | resolves by 'method' |                      |
                 v                      v                      v
     +-----------------------+ +------------------+ +-------------------+
     |   CreditCardGateway   | |  PaypalGateway   | |   PaymobGateway   |
     +-----------------------+ +------------------+ +-------------------+
                 \                      |                     /
                  +---------------------+--------------------+
                                        | implements
                                        v
                       +----------------------------------+
                       |     PaymentGatewayInterface      |
                       +----------------------------------+
```

### Supported Gateways
- `credit_card`: Simulates card transactions (supports mock card numbers, failure simulations).
- `paypal`: Simulates PayPal checkout & capture.
- `paymob`: Simulates Paymob intention and processing.

---

### How to Add a New Payment Gateway (in 2 Steps)

Adding a new payment gateway (e.g., `StripeGateway`) requires **zero changes** to existing payment services or controllers:

#### Step 1: Create the Gateway Class
Create a class implementing `Modules\Payment\Gateways\Contracts\PaymentGatewayInterface`:

```php
namespace Modules\Payment\Gateways;

use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(protected array $config = []) {}

    public function process(Order $order, array $data): array
    {
        // Gateway logic / API communication
        return [
            'success' => true,
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'transaction_id' => 'ch_' . uniqid(),
            'message' => 'Stripe charge captured successfully.',
            'details' => ['gateway' => 'stripe'],
        ];
    }

    public function getIdentifier(): string
    {
        return 'stripe';
    }
}
```

#### Step 2: Register in `config/payment.php`
Add the gateway and its environment credentials to `config/payment.php`:

```php
'gateways' => [
    // ... other gateways
    'stripe' => [
        'class' => \Modules\Payment\Gateways\StripeGateway::class,
        'api_key' => env('STRIPE_SECRET_KEY'),
    ],
],
```

That's it! Clients can now process payments with `"method": "stripe"`.

---

## 📡 API Endpoints Reference

### Authentication
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/register/user` | Register a new user |
| `POST` | `/api/login/mobile` | Login and receive Bearer Token |

### Orders
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/orders` | Create a new order with items |
| `GET` | `/api/orders` | List orders (paginated, supports `?status=pending\|confirmed\|cancelled`) |
| `GET` | `/api/orders/{id}` | Get single order details with items and totals |
| `PUT` | `/api/orders/{id}` | Update order details / status |
| `DELETE` | `/api/orders/{id}` | Delete order (blocked if payments exist) |

### Payments
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/payments` | Process payment simulation for an order (`order_id`, `method`) |
| `GET` | `/api/payments` | List all payments (paginated, supports `?order_id={id}&status={status}`) |
| `GET` | `/api/payments/{id}` | Get single payment details |

---

## 📮 Postman Collection

A complete Postman collection is included in the project root:
- File: [`postman_collection.json`](postman_collection.json)
- Includes folders for **Authentication**, **Orders**, and **Payments**.
- Contains pre-configured environment variables (`URL`, `ACCESS_TOKEN`).
- Pre-script in Login endpoint automatically populates `{{ACCESS_TOKEN}}` upon successful authentication.

---

## 🧪 Running Automated Tests

Run the test suite with PHPUnit:

```bash
php artisan test
```

Specific test suites:
```bash
# Payment Gateway Strategy Pattern Unit Tests
php artisan test --filter=PaymentGatewayStrategyTest

# Order Management & Business Rules Feature Tests
php artisan test --filter=OrderManagementTest

# Payment Processing & Extensibility Feature Tests
php artisan test --filter=PaymentProcessingTest
```
