# Примеры использования для Laravel 11 и 12

Этот документ содержит примеры использования пакета LaravelShoppingcart в Laravel 11 и 12.

## Базовая настройка

### Laravel 11

В Laravel 11 пакет автоматически регистрируется через Package Discovery. Никаких дополнительных действий не требуется.

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware настройки
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### Публикация конфигурации

```bash
php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="config"
```

## Примеры использования

### 1. Базовое добавление товара в корзину

```php
<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        Cart::add(
            id: $request->product_id,
            name: $request->product_name,
            qty: $request->quantity,
            price: $request->price,
            weight: $request->weight ?? 0
        );

        return redirect()->back()->with('success', 'Товар добавлен в корзину!');
    }
}
```

### 2. Использование с моделью (Buyable)

```php
<?php

namespace App\Models;

use Gloudemans\Shoppingcart\Contracts\Buyable;
use Gloudemans\Shoppingcart\CanBeBought;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements Buyable
{
    use CanBeBought;

    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
    ];

    // Trait CanBeBought автоматически реализует необходимые методы:
    // - getBuyableIdentifier()
    // - getBuyableDescription()
    // - getBuyablePrice()
    // - getBuyableWeight()
}
```

```php
// В контроллере
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;

public function addProduct(Product $product)
{
    Cart::add($product, qty: 1, options: ['color' => 'red', 'size' => 'L']);
    
    return redirect()->route('cart.index');
}
```

### 3. Dependency Injection (Laravel 11/12)

```php
<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function __construct(
        private Cart $cart
    ) {}

    public function index()
    {
        $cartContent = $this->cart->content();
        $cartTotal = $this->cart->total();
        
        return view('cart.index', [
            'items' => $cartContent,
            'total' => $cartTotal,
        ]);
    }

    public function add(Product $product)
    {
        $this->cart->add($product);
        
        return redirect()->route('cart.index');
    }

    public function update(string $rowId, int $quantity)
    {
        $this->cart->update($rowId, $quantity);
        
        return redirect()->route('cart.index');
    }

    public function remove(string $rowId)
    {
        $this->cart->remove($rowId);
        
        return redirect()->route('cart.index');
    }
}
```

### 4. Использование событий (Events)

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen('cart.added', function ($item) {
            \Log::info('Товар добавлен в корзину', [
                'id' => $item->id,
                'name' => $item->name,
                'qty' => $item->qty,
            ]);
        });

        Event::listen('cart.updated', function ($item) {
            \Log::info('Товар обновлен в корзине', [
                'rowId' => $item->rowId,
                'qty' => $item->qty,
            ]);
        });

        Event::listen('cart.removed', function ($item) {
            \Log::info('Товар удален из корзины', [
                'rowId' => $item->rowId,
            ]);
        });
    }
}
```

### 5. Сохранение корзины в базу данных

```php
use Gloudemans\Shoppingcart\Facades\Cart;

// Сохранение корзины
public function store()
{
    $user = auth()->user();
    Cart::store($user->id);
    
    return response()->json(['message' => 'Корзина сохранена']);
}

// Восстановление корзины
public function restore()
{
    $user = auth()->user();
    Cart::restore($user->id);
    
    return redirect()->route('cart.index');
}

// Удаление сохраненной корзины
public function erase()
{
    $user = auth()->user();
    Cart::erase($user->id);
    
    return response()->json(['message' => 'Корзина удалена']);
}
```

### 6. Множественные экземпляры корзины

```php
use Gloudemans\Shoppingcart\Facades\Cart;

// Корзина покупок
Cart::instance('shopping')->add($product);

// Список желаний
Cart::instance('wishlist')->add($product);

// Получение содержимого конкретной корзины
$shoppingCart = Cart::instance('shopping')->content();
$wishlist = Cart::instance('wishlist')->content();

// Подсчет товаров в разных корзинах
$shoppingCount = Cart::instance('shopping')->count();
$wishlistCount = Cart::instance('wishlist')->count();
```

### 7. Работа с налогами и скидками

```php
use Gloudemans\Shoppingcart\Facades\Cart;

// Установка глобального налога (для всех товаров)
Cart::setGlobalTax(21); // 21%

// Установка налога для конкретного товара
$rowId = Cart::add($product)->rowId;
Cart::setTax($rowId, 15); // 15%

// Установка глобальной скидки
Cart::setGlobalDiscount(10); // 10%

// Установка скидки для конкретного товара
Cart::setDiscount($rowId, 5); // 5%

// Получение итоговых сумм
$subtotal = Cart::subtotal(); // Сумма без налогов
$tax = Cart::tax(); // Сумма налогов
$total = Cart::total(); // Итоговая сумма с налогами
$discount = Cart::discount(); // Сумма скидки
```

### 8. Blade компонент для отображения корзины (Laravel 11)

```php
// app/View/Components/CartSummary.php
<?php

namespace App\View\Components;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\View\Component;
use Illuminate\View\View;

class CartSummary extends Component
{
    public function render(): View
    {
        return view('components.cart-summary', [
            'items' => Cart::content(),
            'count' => Cart::count(),
            'subtotal' => Cart::subtotal(),
            'tax' => Cart::tax(),
            'total' => Cart::total(),
        ]);
    }
}
```

```blade
{{-- resources/views/components/cart-summary.blade.php --}}
<div class="cart-summary">
    <h3>Корзина ({{ $count }} товаров)</h3>
    
    @foreach($items as $item)
        <div class="cart-item">
            <span>{{ $item->name }}</span>
            <span>{{ $item->qty }} x {{ $item->price }}</span>
            <span>{{ $item->subtotal }}</span>
        </div>
    @endforeach
    
    <div class="cart-totals">
        <div>Подытог: {{ $subtotal }}</div>
        <div>Налог: {{ $tax }}</div>
        <div><strong>Итого: {{ $total }}</strong></div>
    </div>
</div>
```

```blade
{{-- Использование в шаблоне --}}
<x-cart-summary />
```

### 9. API для корзины (Laravel 11/12)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => Cart::content(),
            'count' => Cart::count(),
            'subtotal' => Cart::subtotal(),
            'tax' => Cart::tax(),
            'total' => Cart::total(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $item = Cart::add($product, $validated['quantity']);

        return response()->json([
            'message' => 'Товар добавлен в корзину',
            'item' => $item,
            'cart' => [
                'count' => Cart::count(),
                'total' => Cart::total(),
            ],
        ], 201);
    }

    public function update(Request $request, string $rowId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        Cart::update($rowId, $validated['quantity']);

        return response()->json([
            'message' => 'Корзина обновлена',
            'cart' => [
                'count' => Cart::count(),
                'total' => Cart::total(),
            ],
        ]);
    }

    public function destroy(string $rowId): JsonResponse
    {
        Cart::remove($rowId);

        return response()->json([
            'message' => 'Товар удален из корзины',
            'cart' => [
                'count' => Cart::count(),
                'total' => Cart::total(),
            ],
        ]);
    }

    public function clear(): JsonResponse
    {
        Cart::destroy();

        return response()->json([
            'message' => 'Корзина очищена',
        ]);
    }
}
```

### 10. Middleware для автоматического восстановления корзины

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestoreCart
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            Cart::restore(auth()->id());
        }

        return $next($request);
    }
}
```

```php
// bootstrap/app.php (Laravel 11)
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\RestoreCart::class,
    ]);
})
```

## Дополнительные ресурсы

- [Основная документация](../README.md)
- [Руководство по обновлению](../UPGRADE.md)
- [История изменений](../CHANGELOG.md)
