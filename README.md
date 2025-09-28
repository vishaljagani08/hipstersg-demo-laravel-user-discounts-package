# 🎁 Laravel User Discounts

A **reusable Laravel 12 package** for deterministic user-level discounts with stacking, caps, audits, and concurrency safety.

---

## 📦 Installation

1. **Install via Composer**

   ```bash
   composer require hipstersg-demo/laravel-user-discounts-package
   ```

2. **Publish Config & Migrations**

   ```bash
   php artisan vendor:publish --provider="Vendor\\UserDiscounts\\DiscountServiceProvider" --tag=config
   php artisan migrate
   ```

3. **(Optional) Publish for Customization**

   ```bash
   php artisan vendor:publish --provider="Vendor\\UserDiscounts\\DiscountServiceProvider" --tag=migrations
   php artisan vendor:publish --provider="Vendor\\UserDiscounts\\DiscountServiceProvider" --tag=seeders
   ```

---

## 🛠 Usage

### ➕ Assigning a Discount

```php
use Vendor\UserDiscounts\Models\Discount;
use Vendor\UserDiscounts\Facades\Discounts;

$discount = Discount::create([
    'code' => 'WELCOME10',
    'type' => 'percentage',
    'value' => 10,
    'active' => true,
]);

Discounts::assign($user, $discount);
```

### 🔍 Checking Eligibility

```php
if (Discounts::eligibleFor($user, $discount)) {
    echo "User can use this discount.";
}
```

### 💰 Applying Discounts

```php
$result = Discounts::apply($user, 1500.00, [
    'idempotency_key' => 'order-1001',
    'meta' => ['order_id' => 1001],
]);

echo "Final amount: {$result['amount']}";
```

### ❌ Revoking Discounts

```php
Discounts::revoke($user, $discount);
```

---

## 📡 Events

The package fires events that you can listen for in your app:

* `DiscountAssigned($user, Discount $discount, array $meta)`
* `DiscountRevoked($user, Discount $discount)`
* `DiscountApplied($user, Collection $discounts, DiscountAudit $audit)`

### Example Listener

```php
namespace App\Listeners;

use Vendor\UserDiscounts\Events\DiscountApplied;

class SendDiscountNotification
{
    public function handle(DiscountApplied $event)
    {
        // Access: $event->user, $event->discounts, $event->audit
        // Example: send email or log analytics
    }
}
```

Register in `EventServiceProvider`:

```php
protected $listen = [
    DiscountApplied::class => [SendDiscountNotification::class],
];
```

---

## ⚙️ Configuration

See `config/discounts.php` for options:

* `stacking_order` → `priority | percentage_first | fixed_first`
* `max_total_percentage` → cap on total % discount
* `rounding` → precision + mode (`half_up`, `half_down`, `up`, `down`)
* `default_per_user_cap` → fallback if discount doesn’t define one
* `require_idempotency_key` → enforce unique apply calls


---

## 👨‍💻 Developer

* **Vishal Jagani**
* 📧 [vish2patel08@gmail.com](mailto:vish2patel08@gmail.com)
* 📞 +91 90995 46953
