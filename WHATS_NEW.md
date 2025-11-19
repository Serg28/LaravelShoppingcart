# Что нового в версии для Laravel 11 и 12

## 🎉 Основные изменения

### ✅ Добавлена поддержка Laravel 11 и 12
Пакет теперь полностью совместим с Laravel 11.x и 12.x, сохраняя обратную совместимость с Laravel 9.x и 10.x.

### ✅ Обновлены требования PHP
- Минимальная версия PHP: **8.2**
- Поддержка PHP 8.3

### ✅ Современные зависимости
- **PHPUnit**: 10.x и 11.x
- **Orchestra Testbench**: 7.x - 10.x
- **Carbon**: 2.x и 3.x

### ✅ Улучшенное тестирование
- Обновлен `phpunit.xml` для PHPUnit 10+
- Добавлена матрица тестирования для всех версий Laravel в GitHub Actions
- Улучшено покрытие кода тестами

## 📚 Новая документация

### Добавлены новые файлы документации:
- **UPGRADE.md** - Подробное руководство по обновлению
- **EXAMPLES.md** - Примеры использования для Laravel 11 и 12
- **TESTING.md** - Руководство по тестированию
- **CHANGELOG.md** - История изменений

## 🔧 Технические улучшения

### GitHub Actions
- Обновлен CI/CD workflow для тестирования на PHP 8.2 и 8.3
- Добавлена матрица тестирования для Laravel 9, 10, 11 и 12
- Обновлены actions до версии 4
- Удален устаревший Travis CI

### Конфигурация
- Добавлен `.gitattributes` для правильной работы с Git
- Обновлен `phpunit.xml` для современных стандартов

## 🚀 Миграция с предыдущих версий

### Если вы используете Laravel 9 или 10
Никаких изменений не требуется! Пакет полностью обратно совместим.

### Если вы обновляетесь до Laravel 11 или 12
1. Обновите PHP до версии 8.2 или выше
2. Обновите пакет: `composer update bumbummen99/shoppingcart`
3. Очистите кэш: `php artisan config:clear`

Подробные инструкции см. в [UPGRADE.md](UPGRADE.md)

## 📖 Примеры использования

### Базовое использование (без изменений)
```php
use Gloudemans\Shoppingcart\Facades\Cart;

Cart::add($product, 1);
Cart::content();
Cart::total();
```

### Новый подход с Dependency Injection (Laravel 11/12)
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

Больше примеров в [EXAMPLES.md](EXAMPLES.md)

## 🐛 Исправления

### PostgreSQL
Если вы используете PostgreSQL и обновляетесь с версии < 4.2.0, рекомендуется очистить таблицу корзины из-за изменений в кодировании данных.

## 🤝 Вклад в проект

Мы приветствуем вклад в развитие проекта! Если вы нашли баг или хотите предложить улучшение:
1. Создайте issue на GitHub
2. Отправьте pull request
3. Убедитесь, что все тесты проходят

## 📝 Лицензия

MIT License - без изменений

## 🔗 Полезные ссылки

- [Основная документация](README.md)
- [Руководство по обновлению](UPGRADE.md)
- [Примеры использования](EXAMPLES.md)
- [Руководство по тестированию](TESTING.md)
- [GitHub Repository](https://github.com/bumbummen99/LaravelShoppingcart)
- [Packagist](https://packagist.org/packages/bumbummen99/shoppingcart)

---

**Спасибо за использование LaravelShoppingcart! 🛒**
