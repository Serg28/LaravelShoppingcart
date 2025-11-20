# LaravelShoppingcart

[![CI Code Checks](https://github.com/bumbummen99/LaravelShoppingcart/workflows/CI%20Code%20Checks/badge.svg?branch=master)](https://github.com/bumbummen99/LaravelShoppingcart/actions)
[![codecov](https://codecov.io/gh/bumbummen99/LaravelShoppingcart/branch/master/graph/badge.svg)](https://codecov.io/gh/bumbummen99/LaravelShoppingcart)
[![Total Downloads](https://poser.pugx.org/bumbummen99/shoppingcart/downloads.png)](https://packagist.org/packages/bumbummen99/shoppingcart)
[![Latest Stable Version](https://poser.pugx.org/bumbummen99/shoppingcart/v/stable)](https://packagist.org/packages/bumbummen99/shoppingcart)
[![License](https://poser.pugx.org/bumbummen99/shoppingcart/license)](https://packagist.org/packages/bumbummen99/shoppingcart)

Это форк [LaravelShoppingcart от Crinsane](https://github.com/Crinsane/LaravelShoppingcart), расширенный дополнительными функциями и совместимый с Laravel 9, 10, 11 и 12.

## Требования

- PHP 8.2 или выше
- Laravel 9.x, 10.x, 11.x или 12.x

## Установка

Установите пакет через Composer:

```bash
composer require bumbummen99/shoppingcart
```

### Публикация конфигурации

Рекомендуется опубликовать конфигурационный файл:

```bash
php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="config"
```

Это создаст файл `config/cart.php`, в котором вы можете настроить поведение пакета.

### Публикация миграций

Если вы хотите сохранять корзины в базе данных:

```bash
php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="migrations"
php artisan migrate
```

## Быстрый старт

### Базовое использование

```php
use Gloudemans\Shoppingcart\Facades\Cart;

// Добавить товар в корзину
Cart::add('293ad', 'Товар 1', 1, 9.99, 550);

// Добавить товар с опциями
Cart::add('293ad', 'Товар 1', 1, 9.99, 550, ['size' => 'large', 'color' => 'red']);

// Получить содержимое корзины
$content = Cart::content();

// Получить общую сумму
$total = Cart::total();

// Получить количество товаров
$count = Cart::count();

// Обновить количество товара
Cart::update($rowId, 2);

// Удалить товар
Cart::remove($rowId);

// Очистить корзину
Cart::destroy();
```

### Использование с моделями

```php
use Gloudemans\Shoppingcart\Contracts\Buyable;
use Gloudemans\Shoppingcart\CanBeBought;

class Product extends Model implements Buyable
{
    use CanBeBought;
}

// Добавить модель в корзину
Cart::add($product, 1);
```

### Dependency Injection (Laravel 11/12)

```php
use Gloudemans\Shoppingcart\Cart;

class CartController extends Controller
{
    public function __construct(
        private Cart $cart
    ) {}

    public function index()
    {
        return view('cart', [
            'items' => $this->cart->content(),
            'total' => $this->cart->total(),
        ]);
    }
}
```

## Основные возможности

- ✅ Добавление товаров в корзину
- ✅ Обновление количества товаров
- ✅ Удаление товаров из корзины
- ✅ Расчет налогов и скидок
- ✅ Множественные экземпляры корзины (корзина, список желаний и т.д.)
- ✅ Сохранение корзины в базе данных
- ✅ Восстановление корзины из базы данных
- ✅ Ассоциация товаров с моделями Eloquent
- ✅ События для отслеживания изменений корзины
- ✅ Поддержка опций товаров (размер, цвет и т.д.)
- ✅ Расчет веса корзины
- ✅ Пользовательские калькуляторы цен

## Оглавление

Подробнее о LaravelShoppingcart:

* [Важное примечание](#important-note)
* [Использование](#usage)
* [Коллекции](#collections)
* [Экземпляры](#instances)
* [Модели](#models)
* [База данных](#database)
* [Калькуляторы](#calculators)
* [Исключения](#exceptions)
* [События](#events)
* [Пример](#example)
* [Соавторы](#collaborators)
* [Участники](#contributors)

## Важное примечание

Как и все корзины покупок, которые рассчитывают цены с учетом налогов и скидок, этот модуль также может быть подвержен "проблеме округления итогов" ([*](https://stackoverflow.com/questions/13529580/magento-tax-rounding-issue)) из-за десятичной точности, используемой для цен и результатов.
Чтобы избежать (или хотя бы минимизировать) эту проблему, в пакете Laravel shoppingcart итоги рассчитываются методом **"по строке"** и возвращаются уже округленными на основе формата чисел, установленного по умолчанию в файле конфигурации (cart.php).
В связи с этим **МЫ НЕ РЕКОМЕНДУЕМ УСТАНАВЛИВАТЬ ВЫСОКУЮ ТОЧНОСТЬ ПО УМОЛЧАНИЮ И ФОРМАТИРОВАТЬ ВЫХОДНОЙ РЕЗУЛЬТАТ С МЕНЬШИМ КОЛИЧЕСТВОМ ДЕСЯТИЧНЫХ ЗНАКОВ**. Это может привести к проблеме округления.

Базовая цена (цена товара) остается неокругленной.

## Использование

Корзина предоставляет следующие методы:

### Cart::add()

Добавление товара в корзину очень простое, вы просто используете метод `add()`, который принимает различные параметры.

В самой базовой форме вы можете указать id, название, количество, цену и вес товара, который хотите добавить в корзину.

```php
Cart::add('293ad', 'Товар 1', 1, 9.99, 550);
```

В качестве необязательного пятого параметра вы можете передать опции, чтобы добавить несколько товаров с одинаковым id, но с (например) разным размером.

```php
Cart::add('293ad', 'Товар 1', 1, 9.99, 550, ['size' => 'large']);
```

**Метод `add()` вернет экземпляр CartItem товара, который вы только что добавили в корзину.**

Возможно, вы предпочитаете добавлять товар используя массив? Пока массив содержит необходимые ключи, вы можете передать его в метод. Ключ options необязателен.

```php
Cart::add(['id' => '293ad', 'name' => 'Товар 1', 'qty' => 1, 'price' => 9.99, 'weight' => 550, 'options' => ['size' => 'large']]);
```

Новое в версии 2 пакета - возможность работы с интерфейсом [Buyable](#buyable). Это работает так: ваша модель реализует интерфейс [Buyable](#buyable), который заставит вас реализовать несколько методов, чтобы пакет знал, как получить id, название и цену из вашей модели.
Таким образом, вы можете просто передать методу `add()` модель и количество, и он автоматически добавит ее в корзину.

**В качестве дополнительного бонуса он автоматически свяжет модель с CartItem**

```php
Cart::add($product, 1, ['size' => 'large']);
```

В качестве необязательного третьего параметра вы можете добавить опции.

Наконец, вы также можете добавить несколько товаров в корзину одновременно.
Вы можете просто передать методу `add()` массив массивов или массив Buyables, и они будут добавлены в корзину.

**При добавлении нескольких товаров в корзину метод `add()` вернет массив CartItems.**

```php
Cart::add([
  ['id' => '293ad', 'name' => 'Товар 1', 'qty' => 1, 'price' => 10.00, 'weight' => 550],
  ['id' => '4832k', 'name' => 'Товар 2', 'qty' => 1, 'price' => 10.00, 'weight' => 550, 'options' => ['size' => 'large']]
]);

Cart::add([$product1, $product2]);
```

### Cart::update()

Чтобы обновить товар в корзине, вам сначала понадобится rowId товара.
Затем вы можете использовать метод `update()` для его обновления.

Если вы просто хотите обновить количество, вы передадите методу update rowId и новое количество:

```php
$rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::update($rowId, 2); // Обновит количество
```

Если вы хотите обновить опции товара в корзине:

```php
$rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::update($rowId, ['options' => ['size' => 'small']]); // Обновит опцию размера новым значением
```

Если вы хотите обновить больше атрибутов товара, вы можете передать методу update массив или `Buyable` в качестве второго параметра. Таким образом, вы можете обновить всю информацию о товаре с заданным rowId.

```php
Cart::update($rowId, ['name' => 'Товар 1']); // Обновит название

Cart::update($rowId, $product); // Обновит id, название и цену
```

### Cart::remove()

Чтобы удалить товар из корзины, вам снова понадобится rowId. Этот rowId вы просто передаете методу `remove()`, и он удалит товар из корзины.

```php
$rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::remove($rowId);
```

### Cart::get()

Если вы хотите получить товар из корзины, используя его rowId, вы можете просто вызвать метод `get()` на корзине и передать ему rowId.

```php
$rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::get($rowId);
```

### Cart::content()

Конечно, вы также хотите получить содержимое корзины. Для этого вы используете метод `content`. Этот метод вернет коллекцию CartItems, которую вы можете перебирать и показывать содержимое вашим клиентам.

```php
Cart::content();
```

Этот метод вернет содержимое текущего экземпляра корзины, если вы хотите содержимое другого экземпляра, просто свяжите вызовы.

```php
Cart::instance('wishlist')->content();
```

### Cart::destroy()

Если вы хотите полностью удалить содержимое корзины, вы можете вызвать метод destroy на корзине. Это удалит все CartItems из корзины для текущего экземпляра корзины.

```php
Cart::destroy();
```

### Cart::weight()

Метод `weight()` можно использовать для получения общего веса всех товаров в корзине с учетом их веса и количества.

```php
Cart::weight();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров

```php
Cart::weight($decimals, $decimalSeperator, $thousandSeperator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство total `$cart->weight`**

### Cart::total()

Метод `total()` можно использовать для получения рассчитанной суммы всех товаров в корзине с учетом их цены и количества.

```php
Cart::total();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров

```php
Cart::total($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство total `$cart->total`**

### Cart::tax()

Метод `tax()` можно использовать для получения рассчитанной суммы налога для всех товаров в корзине с учетом их цены и количества.

```php
Cart::tax();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров

```php
Cart::tax($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство tax `$cart->tax`**

### Cart::subtotal()

Метод `subtotal()` можно использовать для получения суммы всех товаров в корзине за вычетом общей суммы налога.

```php
Cart::subtotal();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров

```php
Cart::subtotal($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство subtotal `$cart->subtotal`**

### Cart::discount()

Метод `discount()` можно использовать для получения общей скидки на все товары в корзине.

```php
Cart::discount();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров

```php
Cart::discount($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство subtotal `$cart->discount`**

### Cart::initial()

Метод `initial()` можно использовать для получения общей цены всех товаров в корзине до применения скидки и налогов.

Он может быть устаревшим в будущем. **При округлении может быть подвержен проблеме округления**, используйте его осторожно или используйте [Cart::priceTotal()](#cartpricetotal)

```php
Cart::initial();
```

Метод автоматически отформатирует результат, который вы можете настроить с помощью трех необязательных параметров.

```php
Cart::initial($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

### Cart::priceTotal()

Метод `priceTotal()` можно использовать для получения общей цены всех товаров в корзине до применения скидки и налогов.

```php
Cart::priceTotal();
```

Метод возвращает результат округленным на основе формата чисел по умолчанию, но вы можете настроить его с помощью трех необязательных параметров

```php
Cart::priceTotal($decimals, $decimalSeparator, $thousandSeparator);
```

Вы можете установить формат чисел по умолчанию в файле конфигурации.

**Если вы не используете Facade, а используете внедрение зависимостей в вашем (например) контроллере, вы также можете просто получить свойство subtotal `$cart->initial`**

### Cart::count()

Если вы хотите узнать, сколько товаров в вашей корзине, вы можете использовать метод `count()`. Этот метод вернет общее количество товаров в корзине. Так что если вы добавили 2 книги и 1 рубашку, он вернет 3 товара.

```php
Cart::count();
$cart->count();
```

### Cart::search()

Чтобы найти товар в корзине, вы можете использовать метод `search()`.

**Этот метод был изменен в версии 2**

За кулисами метод просто использует метод filter класса Laravel Collection. Это означает, что вы должны передать ему замыкание (Closure), в котором вы укажете условия поиска.

Например, если вы хотите найти все товары с id 1:

```php
$cart->search(function ($cartItem, $rowId) {
    return $cartItem->id === 1;
});
```

Как видите, замыкание получит два параметра. Первый - это CartItem для выполнения проверки. Второй параметр - это rowId этого CartItem.

**Метод вернет коллекцию, содержащую все найденные CartItems**

Такой способ поиска дает вам полный контроль над процессом поиска и дает возможность создавать очень точные и конкретные поиски.

### Cart::setTax($rowId, $taxRate)

Вы можете использовать метод `setTax()` для изменения налоговой ставки, применяемой к CartItem. Это перезапишет значение, установленное в файле конфигурации.

```php
Cart::setTax($rowId, 21);
$cart->setTax($rowId, 21);
```

### Cart::setGlobalTax($taxRate)

Вы можете использовать метод `setGlobalTax()` для изменения налоговой ставки для всех товаров в корзине. Новые товары также получат setGlobalTax.

```php
Cart::setGlobalTax(21);
$cart->setGlobalTax(21);
```

### Cart::setGlobalDiscount($discountRate)

Вы можете использовать метод `setGlobalDiscount()` для изменения ставки скидки для всех товаров в корзине. Новые товары также получат скидку.

```php
Cart::setGlobalDiscount(50);
$cart->setGlobalDiscount(50);
```

### Cart::setDiscount($rowId, $taxRate)

Вы можете использовать метод `setDiscount()` для изменения ставки скидки, применяемой к CartItem. Имейте в виду, что это значение будет изменено, если вы установите глобальную скидку для корзины после этого.

```php
Cart::setDiscount($rowId, 21);
$cart->setDiscount($rowId, 21);
```

### Buyable

Для удобства более быстрого добавления товаров в корзину и их автоматической ассоциации, ваша модель должна реализовать интерфейс `Buyable`. Вы можете использовать трейт `CanBeBought` для реализации необходимых методов, но имейте в виду, что они будут использовать предопределенные поля в вашей модели для необходимых значений.

```php
<?php
namespace App\Models;

use Gloudemans\Shoppingcart\Contracts\Buyable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements Buyable {
    use Gloudemans\Shoppingcart\CanBeBought;
}
```

Если трейт не работает на модели или вы хотите вручную сопоставить поля, модель должна реализовать методы интерфейса `Buyable`. Для этого она должна реализовать такие функции:

```php
public function getBuyableIdentifier(){
    return $this->id;
}
public function getBuyableDescription(){
    return $this->name;
}
public function getBuyablePrice(){
    return $this->price;
}
public function getBuyableWeight(){
    return $this->weight;
}
```

Пример:

```php
<?php
namespace App\Models;

use Gloudemans\Shoppingcart\Contracts\Buyable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements Buyable {
    public function getBuyableIdentifier($options = null) {
        return $this->id;
    }
    public function getBuyableDescription($options = null) {
        return $this->name;
    }
    public function getBuyablePrice($options = null) {
        return $this->price;
    }
    public function getBuyableWeight($options = null) {
        return $this->weight;
    }
}
```

## Коллекции

В нескольких случаях корзина вернет вам коллекцию. Это просто обычная Laravel Collection, поэтому все методы, которые вы можете вызвать на Laravel Collection, также доступны для результата.

Например, вы можете быстро получить количество уникальных товаров в корзине:

```php
Cart::content()->count();
```

Или вы можете сгруппировать содержимое по id товаров:

```php
Cart::content()->groupBy('id');
```

## Экземпляры

Пакет поддерживает несколько экземпляров корзины. Это работает следующим образом:

Вы можете установить текущий экземпляр корзины, вызвав `Cart::instance('newInstance')`. С этого момента активным экземпляром корзины будет `newInstance`, поэтому когда вы добавляете, удаляете или получаете содержимое корзины, вы работаете с экземпляром `newInstance` корзины.
Если вы хотите переключить экземпляры, вы просто снова вызываете `Cart::instance('otherInstance')`, и вы снова работаете с `otherInstance`.

Небольшой пример:

```php
Cart::instance('shopping')->add('192ao12', 'Товар 1', 1, 9.99, 550);

// Получить содержимое корзины 'shopping'
Cart::content();

Cart::instance('wishlist')->add('sdjk922', 'Товар 2', 1, 19.95, 550, ['size' => 'medium']);

// Получить содержимое корзины 'wishlist'
Cart::content();

// Если вы хотите снова получить содержимое корзины 'shopping'
Cart::instance('shopping')->content();

// И количество корзины 'wishlist' снова
Cart::instance('wishlist')->count();
```

Вы также можете использовать контракт `InstanceIdentifier` для расширения желаемой модели для назначения / создания экземпляра корзины для нее. Это также позволяет напрямую установить глобальную скидку.

```php
<?php

namespace App;
...
use Illuminate\Foundation\Auth\User as Authenticatable;
use Gloudemans\Shoppingcart\Contracts\InstanceIdentifier;

class User extends Authenticatable implements InstanceIdentifier
{
    ...

    /**
     * Получить уникальный идентификатор для загрузки корзины
     *
     * @return int|string
     */
    public function getInstanceIdentifier($options = null)
    {
        return $this->email;
    }

    /**
     * Получить уникальный идентификатор для загрузки корзины
     *
     * @return int|string
     */
    public function getInstanceGlobalDiscount($options = null)
    {
        return $this->discountRate ?: 0;
    }
}

// Внутри контроллера
$user = \Auth::user();
$cart = Cart::instance($user);
```

**Примечание: имейте в виду, что корзина остается в последнем установленном экземпляре до тех пор, пока вы не установите другой во время выполнения скрипта.**

**Примечание 2: экземпляр корзины по умолчанию называется `default`, поэтому когда вы не используете экземпляры, `Cart::content();` то же самое, что и `Cart::instance('default')->content()`.**

## Модели

Поскольку может быть очень удобно иметь возможность напрямую обращаться к модели из CartItem, можно связать модель с товарами в корзине. Допустим, у вас есть модель `Product` в вашем приложении. С помощью метода `associate()` вы можете сообщить корзине, что товар в корзине связан с моделью `Product`.

Таким образом, вы можете получить доступ к вашей модели прямо из `CartItem`!

Доступ к модели можно получить через свойство `model` на CartItem.

**Если ваша модель реализует интерфейс `Buyable` и вы использовали вашу модель для добавления товара в корзину, она будет связана автоматически.**

Вот пример:

```php
// Сначала добавим товар в корзину.
$cartItem = Cart::add('293ad', 'Товар 1', 1, 9.99, 550, ['size' => 'large']);

// Затем свяжем модель с товаром.
Cart::associate($cartItem->rowId, 'Product');

// Или еще проще, вызовите метод associate на CartItem!
$cartItem->associate('Product');

// Вы даже можете сделать это в одну строку
Cart::add('293ad', 'Товар 1', 1, 9.99, 550, ['size' => 'large'])->associate('Product');

// Теперь, при переборе содержимого корзины, вы можете получить доступ к модели.
foreach(Cart::content() as $row) {
    echo 'У вас ' . $row->qty . ' товаров ' . $row->model->name . ' с описанием: "' . $row->model->description . '" в вашей корзине.';
}
```

## База данных

- [Конфигурация](#configuration)
- [Сохранение корзины](#storing-the-cart)
- [Восстановление корзины](#restoring-the-cart)

### Конфигурация

Чтобы сохранить корзину в базу данных, чтобы вы могли получить ее позже, пакет должен знать, какое подключение к базе данных использовать и какое имя таблицы.
По умолчанию пакет будет использовать подключение к базе данных по умолчанию и использовать таблицу с именем `shoppingcart`. Вы можете изменить это в конфигурации.

Чтобы облегчить вашу жизнь, пакет также включает готовую к использованию `миграцию`, которую вы можете опубликовать, запустив:

    php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="migrations"
    
Это поместит файл миграции таблицы `shoppingcart` в каталог `database/migrations`. Теперь все, что вам нужно сделать, это запустить `php artisan migrate` для миграции вашей базы данных.

### Сохранение корзины

Чтобы сохранить экземпляр корзины в базу данных, вы должны вызвать метод `store($identifier)`. Где `$identifier` - это случайный ключ, например, id или имя пользователя.

    Cart::store('username');
    
    // Чтобы сохранить экземпляр корзины с именем 'wishlist'
    Cart::instance('wishlist')->store('username');

### Восстановление корзины

Если вы хотите получить корзину из базы данных и восстановить ее, все, что вам нужно сделать, это вызвать `restore($identifier)`, где `$identifier` - это ключ, который вы указали для метода `store`.
 
    Cart::restore('username');
    
    // Чтобы восстановить экземпляр корзины с именем 'wishlist'
    Cart::instance('wishlist')->restore('username');

### Слияние корзины

Если вы хотите объединить корзину с другой из базы данных, все, что вам нужно сделать, это вызвать `merge($identifier)`, где `$identifier` - это ключ, который вы указали для метода `store`. Вы также можете определить, хотите ли вы сохранить скидку и налоговые ставки товаров и хотите ли вы отправлять события "cart.added".
     
    // Объединить содержимое 'savedcart' в 'username'.
    Cart::instance('username')->merge('savedcart', $keepDiscount, $keepTaxrate, $dispatchAdd, 'savedcartinstance');

### Удаление корзины

Если вы хотите удалить корзину из базы данных, все, что вам нужно сделать, это вызвать `erase($identifier)`, где `$identifier` - это ключ, который вы указали для метода `store`.
 
    Cart::erase('username');
    
    // Чтобы удалить корзину, переключившись на экземпляр с именем 'wishlist'
    Cart::instance('wishlist')->erase('username');

## Калькуляторы

Логика расчета для пакета реализована и определена в классах `Calculator`. Они реализуют контракт `Gloudemans\Shoppingcart\Contracts\Calculator` и определяют, как рассчитываются и округляются цены. Калькуляторы можно настроить в файле конфигурации. Это калькулятор по умолчанию:

```php
<?php

namespace Gloudemans\Shoppingcart\Calculation;

use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Contracts\Calculator;

class DefaultCalculator implements Calculator
{
    public static function getAttribute(string $attribute, CartItem $cartItem)
    {
        $decimals = config('cart.format.decimals', 2);

        switch ($attribute) {
            case 'discount':
                return $cartItem->price * ($cartItem->getDiscountRate() / 100);
            case 'tax':
                return round($cartItem->priceTarget * ($cartItem->taxRate / 100), $decimals);
            case 'priceTax':
                return round($cartItem->priceTarget + $cartItem->tax, $decimals);
            case 'discountTotal':
                return round($cartItem->discount * $cartItem->qty, $decimals);
            case 'priceTotal':
                return round($cartItem->price * $cartItem->qty, $decimals);
            case 'subtotal':
                return max(round($cartItem->priceTotal - $cartItem->discountTotal, $decimals), 0);
            case 'priceTarget':
                return round(($cartItem->priceTotal - $cartItem->discountTotal) / $cartItem->qty, $decimals);
            case 'taxTotal':
                return round($cartItem->subtotal * ($cartItem->taxRate / 100), $decimals);
            case 'total':
                return round($cartItem->subtotal + $cartItem->taxTotal, $decimals);
            default:
                return;
        }
    }
}
```

## Исключения

Пакет корзины будет выбрасывать исключения, если что-то пойдет не так. Таким образом, легче отлаживать ваш код, используя пакет корзины, или обрабатывать ошибку на основе типа исключений. Пакет корзины может выбрасывать следующие исключения:

| Исключение                    | Причина                                                                             |
| ---------------------------- | ---------------------------------------------------------------------------------- |
| *CartAlreadyStoredException* | При попытке сохранить корзину, которая уже была сохранена с использованием указанного идентификатора |
| *InvalidRowIDException*      | Когда переданный rowId не существует в текущем экземпляре корзины         |
| *UnknownModelException*      | Когда вы пытаетесь связать несуществующую модель с CartItem.                    |

## События

Корзина также имеет встроенные события. Доступно десять событий для прослушивания.

| Событие       | Срабатывает                                    | Параметр                             |
| ------------- | ---------------------------------------- | ------------------------------------- |
| cart.adding   | При добавлении товара в корзину.         | `CartItem`, который добавляется.   |
| cart.updating | При обновлении товара в корзине.       | `CartItem`, который обновляется. |
| cart.removing | При удалении товара из корзины.       | `CartItem`, который удаляется. |
| cart.added    | Когда товар был добавлен в корзину.      | `CartItem`, который был добавлен.        |
| cart.updated  | Когда товар был обновлен в корзине.    | `CartItem`, который был обновлен.      |
| cart.removed  | Когда товар был удален из корзины.  | `CartItem`, который был удален.      |
| cart.merged   | Когда содержимое корзины объединено     | -                                     |
| cart.stored   | Когда содержимое корзины было сохранено.   | -                                     |
| cart.restored | Когда содержимое корзины было восстановлено. | -                                     |
| cart.erased   | Когда содержимое корзины было удалено.   | -                                     |

## Пример

Ниже приведен небольшой пример того, как отобразить содержимое корзины в таблице:

```php
// Добавьте несколько товаров в вашем контроллере.
Cart::add('192ao12', 'Товар 1', 1, 9.99);
Cart::add('1239ad0', 'Товар 2', 2, 5.95, ['size' => 'large']);

// Отобразите содержимое в представлении.
<table>
    <thead>
        <tr>
            <th>Товар</th>
            <th>Кол-во</th>
            <th>Цена</th>
            <th>Промежуточный итог</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach(Cart::content() as $row) :?>

            <tr>
                <td>
                    <p><strong><?php echo $row->name; ?></strong></p>
                    <p><?php echo ($row->options->has('size') ? $row->options->size : ''); ?></p>
                </td>
                <td><input type="text" value="<?php echo $row->qty; ?>"></td>
                <td>$<?php echo $row->price; ?></td>
                <td>$<?php echo $row->total; ?></td>
            </tr>

        <?php endforeach;?>

    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td>Промежуточный итог</td>
            <td><?php echo Cart::subtotal(); ?></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td>Налог</td>
            <td><?php echo Cart::tax(); ?></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td>Итого</td>
            <td><?php echo Cart::total(); ?></td>
        </tr>
    </tfoot>
</table>
```

## Соавторы

<!-- readme: collaborators -start -->
<table>
<tr>
    <td align="center">
        <a href="https://github.com/bumbummen99">
            <img src="https://avatars.githubusercontent.com/u/4533331?v=4" width="100;" alt="bumbummen99"/>
            <br />
            <sub><b>Patrick</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/Sartoric">
            <img src="https://avatars.githubusercontent.com/u/6607379?v=4" width="100;" alt="Sartoric"/>
            <br />
            <sub><b>Sartoric</b></sub>
        </a>
    </td></tr>
</table>
<!-- readme: collaborators -end -->

## Участники
<!-- readme: contributors -start -->
<table>
<tr>
    <td align="center">
        <a href="https://github.com/bumbummen99">
            <img src="https://avatars.githubusercontent.com/u/4533331?v=4" width="100;" alt="bumbummen99"/>
            <br />
            <sub><b>Patrick</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/Crinsane">
            <img src="https://avatars.githubusercontent.com/u/1297781?v=4" width="100;" alt="Crinsane"/>
            <br />
            <sub><b>Rob Gloudemans</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/Norris1z">
            <img src="https://avatars.githubusercontent.com/u/18237132?v=4" width="100;" alt="Norris1z"/>
            <br />
            <sub><b>Norris Oduro</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/olegbespalov">
            <img src="https://avatars.githubusercontent.com/u/5425600?v=4" width="100;" alt="olegbespalov"/>
            <br />
            <sub><b>Oleg Bespalov</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/cwprogger">
            <img src="https://avatars.githubusercontent.com/u/11742147?v=4" width="100;" alt="cwprogger"/>
            <br />
            <sub><b>Andrew Savchenko</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/ChrisThompsonTLDR">
            <img src="https://avatars.githubusercontent.com/u/348801?v=4" width="100;" alt="ChrisThompsonTLDR"/>
            <br />
            <sub><b>Chris Thompson</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/Jam-Iko">
            <img src="https://avatars.githubusercontent.com/u/44161368?v=4" width="100;" alt="Jam-Iko"/>
            <br />
            <sub><b>Jam-Iko</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/mattusik">
            <img src="https://avatars.githubusercontent.com/u/1252223?v=4" width="100;" alt="mattusik"/>
            <br />
            <sub><b>Matus Rohal</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/rakibabu">
            <img src="https://avatars.githubusercontent.com/u/14089150?v=4" width="100;" alt="rakibabu"/>
            <br />
            <sub><b>Rakhal Imming</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/tiotobing">
            <img src="https://avatars.githubusercontent.com/u/33707075?v=4" width="100;" alt="tiotobing"/>
            <br />
            <sub><b>Tiotobing</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/Sartoric">
            <img src="https://avatars.githubusercontent.com/u/6607379?v=4" width="100;" alt="Sartoric"/>
            <br />
            <sub><b>Sartoric</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/macbookandrew">
            <img src="https://avatars.githubusercontent.com/u/784333?v=4" width="100;" alt="macbookandrew"/>
            <br />
            <sub><b>Andrew Minion</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/dtwebuk">
            <img src="https://avatars.githubusercontent.com/u/6045378?v=4" width="100;" alt="dtwebuk"/>
            <br />
            <sub><b>Daniel Tomlinson</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/tkaw220">
            <img src="https://avatars.githubusercontent.com/u/694289?v=4" width="100;" alt="tkaw220"/>
            <br />
            <sub><b>Edwin Aw</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/manojo123">
            <img src="https://avatars.githubusercontent.com/u/20805943?v=4" width="100;" alt="manojo123"/>
            <br />
            <sub><b>Jorge Moura</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/jorgejavierleon">
            <img src="https://avatars.githubusercontent.com/u/7950376?v=4" width="100;" alt="jorgejavierleon"/>
            <br />
            <sub><b>Jorge Javier León</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/geisi">
            <img src="https://avatars.githubusercontent.com/u/10728579?v=4" width="100;" alt="geisi"/>
            <br />
            <sub><b>Tim Geisendörfer</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/adamgoose">
            <img src="https://avatars.githubusercontent.com/u/611068?v=4" width="100;" alt="adamgoose"/>
            <br />
            <sub><b>Adam Engebretson</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/andcl">
            <img src="https://avatars.githubusercontent.com/u/8470427?v=4" width="100;" alt="andcl"/>
            <br />
            <sub><b>Andrés</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/ganyicz">
            <img src="https://avatars.githubusercontent.com/u/3823354?v=4" width="100;" alt="ganyicz"/>
            <br />
            <sub><b>Filip Ganyicz</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/guysolamour">
            <img src="https://avatars.githubusercontent.com/u/22590722?v=4" width="100;" alt="guysolamour"/>
            <br />
            <sub><b>Guy-roland ASSALE</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/jackmcdade">
            <img src="https://avatars.githubusercontent.com/u/44739?v=4" width="100;" alt="jackmcdade"/>
            <br />
            <sub><b>Jack McDade</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/jeremyvaught">
            <img src="https://avatars.githubusercontent.com/u/302304?v=4" width="100;" alt="jeremyvaught"/>
            <br />
            <sub><b>Jeremy Vaught</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/jmarkese">
            <img src="https://avatars.githubusercontent.com/u/1827586?v=4" width="100;" alt="jmarkese"/>
            <br />
            <sub><b>John Markese</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/nexxai">
            <img src="https://avatars.githubusercontent.com/u/4316564?v=4" width="100;" alt="nexxai"/>
            <br />
            <sub><b>JT Smith</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/mrabbani">
            <img src="https://avatars.githubusercontent.com/u/4253979?v=4" width="100;" alt="mrabbani"/>
            <br />
            <sub><b>Mahbub Rabbani</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/mauriciv">
            <img src="https://avatars.githubusercontent.com/u/12043163?v=4" width="100;" alt="mauriciv"/>
            <br />
            <sub><b>Mauricio Vera</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/xpundel">
            <img src="https://avatars.githubusercontent.com/u/1384653?v=4" width="100;" alt="xpundel"/>
            <br />
            <sub><b>Mikhail Lisnyak</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/absemetov">
            <img src="https://avatars.githubusercontent.com/u/735924?v=4" width="100;" alt="absemetov"/>
            <br />
            <sub><b>Nadir Absemetov</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/nielsiano">
            <img src="https://avatars.githubusercontent.com/u/947684?v=4" width="100;" alt="nielsiano"/>
            <br />
            <sub><b>Niels Stampe</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/4ilo">
            <img src="https://avatars.githubusercontent.com/u/15938739?v=4" width="100;" alt="4ilo"/>
            <br />
            <sub><b>Olivier</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/PazkaL">
            <img src="https://avatars.githubusercontent.com/u/1322192?v=4" width="100;" alt="PazkaL"/>
            <br />
            <sub><b>Pascal Kousbroek</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/quintenbuis">
            <img src="https://avatars.githubusercontent.com/u/36452184?v=4" width="100;" alt="quintenbuis"/>
            <br />
            <sub><b>Quinten Buis</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/publiux">
            <img src="https://avatars.githubusercontent.com/u/2847188?v=4" width="100;" alt="publiux"/>
            <br />
            <sub><b>Raul Ruiz</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/royduin">
            <img src="https://avatars.githubusercontent.com/u/1703233?v=4" width="100;" alt="royduin"/>
            <br />
            <sub><b>Roy Duineveld</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/CaddyDz">
            <img src="https://avatars.githubusercontent.com/u/13698160?v=4" width="100;" alt="CaddyDz"/>
            <br />
            <sub><b>Salim Djerbouh</b></sub>
        </a>
    </td></tr>
<tr>
    <td align="center">
        <a href="https://github.com/pendalff">
            <img src="https://avatars.githubusercontent.com/u/236587?v=4" width="100;" alt="pendalff"/>
            <br />
            <sub><b>Fukalov Sem</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/sobhanatar">
            <img src="https://avatars.githubusercontent.com/u/1507325?v=4" width="100;" alt="sobhanatar"/>
            <br />
            <sub><b>Sobhan Atar</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/mightyteja">
            <img src="https://avatars.githubusercontent.com/u/2662727?v=4" width="100;" alt="mightyteja"/>
            <br />
            <sub><b>Teja Babu S</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/kekenec">
            <img src="https://avatars.githubusercontent.com/u/11806874?v=4" width="100;" alt="kekenec"/>
            <br />
            <sub><b>Kekenec</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/sasin91">
            <img src="https://avatars.githubusercontent.com/u/808722?v=4" width="100;" alt="sasin91"/>
            <br />
            <sub><b>Sasin91</b></sub>
        </a>
    </td></tr>
</table>
<!-- readme: contributors -end -->

