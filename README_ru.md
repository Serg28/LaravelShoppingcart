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

## Документация

- 📖 [Полная документация](README.md) - Подробное описание всех возможностей
- 🚀 [Руководство по обновлению](UPGRADE.md) - Инструкции по обновлению до Laravel 11/12
- 💡 [Примеры использования](EXAMPLES.md) - Практические примеры для Laravel 11/12
- 🧪 [Руководство по тестированию](TESTING.md) - Как запускать и писать тесты
- 📝 [История изменений](CHANGELOG.md) - Что нового в каждой версии
- 🎉 [Что нового](WHATS_NEW.md) - Краткий обзор последних изменений

## Обновление до Laravel 11/12

Если вы обновляетесь с предыдущих версий:

1. Обновите PHP до версии 8.2 или выше
2. Обновите пакет:
   ```bash
   composer update bumbummen99/shoppingcart
   ```
3. Очистите кэш:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

Подробные инструкции см. в [UPGRADE.md](UPGRADE.md)

## Примеры

### Работа с налогами и скидками

```php
// Установить глобальный налог
Cart::setGlobalTax(21); // 21%

// Установить глобальную скидку
Cart::setGlobalDiscount(10); // 10%

// Получить суммы
$subtotal = Cart::subtotal(); // Без налогов
$tax = Cart::tax(); // Сумма налогов
$total = Cart::total(); // С налогами
```

### Множественные корзины

```php
// Корзина покупок
Cart::instance('shopping')->add($product);

// Список желаний
Cart::instance('wishlist')->add($product);

// Получить содержимое
$shopping = Cart::instance('shopping')->content();
$wishlist = Cart::instance('wishlist')->content();
```

### Сохранение в базу данных

```php
// Сохранить корзину
Cart::store(auth()->id());

// Восстановить корзину
Cart::restore(auth()->id());

// Удалить сохраненную корзину
Cart::erase(auth()->id());
```

Больше примеров в [EXAMPLES.md](EXAMPLES.md)

## Тестирование

```bash
# Установить зависимости
composer install

# Запустить тесты
vendor/bin/phpunit

# С покрытием кода
vendor/bin/phpunit --coverage-html coverage
```

Подробнее в [TESTING.md](TESTING.md)

## Вклад в проект

Мы приветствуем вклад в развитие проекта! Пожалуйста:

1. Форкните репозиторий
2. Создайте ветку для вашей функции (`git checkout -b feature/amazing-feature`)
3. Зафиксируйте изменения (`git commit -m 'Add amazing feature'`)
4. Отправьте в ветку (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

Убедитесь, что все тесты проходят перед отправкой PR.

## Лицензия

MIT License. См. [LICENSE](LICENSE) для подробностей.

## Авторы

- **Rob Gloudemans** - Оригинальный автор
- **Patrick Henninger** - Мейнтейнер форка
- **Все участники** - См. [Contributors](https://github.com/bumbummen99/LaravelShoppingcart/graphs/contributors)

## Поддержка

- 🐛 [Сообщить о проблеме](https://github.com/bumbummen99/LaravelShoppingcart/issues)
- 💬 [Обсуждения](https://github.com/bumbummen99/LaravelShoppingcart/discussions)
- 📧 [Email](mailto:privat@skyraptor.eu)

## Ссылки

- [GitHub](https://github.com/bumbummen99/LaravelShoppingcart)
- [Packagist](https://packagist.org/packages/bumbummen99/shoppingcart)
- [Пример интеграции](https://github.com/bumbummen99/LaravelShoppingcartDemo)

---

**Спасибо за использование LaravelShoppingcart! 🛒**
