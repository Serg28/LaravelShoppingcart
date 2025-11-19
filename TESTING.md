# Руководство по тестированию

Этот документ описывает, как запускать тесты для пакета LaravelShoppingcart.

## Требования для тестирования

- PHP 8.2 или выше
- Composer
- SQLite (для тестовой базы данных)

## Установка зависимостей для разработки

```bash
composer install
```

Это установит все необходимые зависимости, включая PHPUnit и Orchestra Testbench.

## Запуск тестов

### Запуск всех тестов

```bash
vendor/bin/phpunit
```

### Запуск тестов с покрытием кода

```bash
vendor/bin/phpunit --coverage-html coverage
```

Отчет о покрытии будет сохранен в директории `coverage/`.

### Запуск тестов с покрытием (текстовый формат)

```bash
vendor/bin/phpunit --coverage-text
```

### Запуск конкретного теста

```bash
vendor/bin/phpunit --filter test_name
```

Например:

```bash
vendor/bin/phpunit --filter it_can_add_an_item
```

### Запуск тестов для конкретного класса

```bash
vendor/bin/phpunit tests/CartTest.php
```

## Тестирование с разными версиями Laravel

### Laravel 9

```bash
composer require "laravel/framework:^9.0" "orchestra/testbench:^7.0" --dev --no-update
composer update
vendor/bin/phpunit
```

### Laravel 10

```bash
composer require "laravel/framework:^10.0" "orchestra/testbench:^8.0" --dev --no-update
composer update
vendor/bin/phpunit
```

### Laravel 11

```bash
composer require "laravel/framework:^11.0" "orchestra/testbench:^9.0" --dev --no-update
composer update
vendor/bin/phpunit
```

### Laravel 12

```bash
composer require "laravel/framework:^12.0" "orchestra/testbench:^10.0" --dev --no-update
composer update
vendor/bin/phpunit
```

## Структура тестов

```
tests/
├── CartAssertions.php          # Вспомогательные методы для тестирования
├── CartItemTest.php            # Тесты для CartItem
├── CartTest.php                # Основные тесты для Cart
└── Fixtures/                   # Тестовые фикстуры
    ├── BuyableProduct.php
    ├── BuyableProductTrait.php
    ├── Identifiable.php
    └── ProductModel.php
```

## Написание собственных тестов

Если вы хотите расширить пакет и написать свои тесты, используйте Orchestra Testbench:

```php
<?php

namespace Gloudemans\Tests\Shoppingcart;

use Gloudemans\Shoppingcart\ShoppingcartServiceProvider;
use Orchestra\Testbench\TestCase;

class MyCustomTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ShoppingcartServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cart.database.connection', 'testing');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /** @test */
    public function my_custom_test()
    {
        // Ваш тест здесь
        $this->assertTrue(true);
    }
}
```

## Continuous Integration

Проект использует GitHub Actions для автоматического тестирования. При каждом push или pull request автоматически запускаются тесты на разных версиях PHP и Laravel.

Конфигурация CI находится в `.github/workflows/php.yml`.

## Отладка тестов

### Вывод дополнительной информации

```bash
vendor/bin/phpunit --verbose
```

### Остановка на первой ошибке

```bash
vendor/bin/phpunit --stop-on-failure
```

### Вывод всех ошибок

```bash
vendor/bin/phpunit --testdox
```

## Проверка стиля кода

Если в проекте настроен PHP CS Fixer или другой инструмент для проверки стиля кода:

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

## Анализ кода

Для статического анализа кода можно использовать PHPStan или Psalm (если они установлены):

```bash
# PHPStan
vendor/bin/phpstan analyse

# Psalm
vendor/bin/psalm
```

## Проблемы и решения

### Ошибка "Class not found"

Убедитесь, что все зависимости установлены:

```bash
composer install
composer dump-autoload
```

### Ошибки с базой данных

Убедитесь, что SQLite установлен:

```bash
# Ubuntu/Debian
sudo apt-get install php8.2-sqlite3

# macOS
brew install sqlite
```

### Ошибки с памятью

Увеличьте лимит памяти для PHP:

```bash
php -d memory_limit=512M vendor/bin/phpunit
```

## Дополнительные ресурсы

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Orchestra Testbench](https://github.com/orchestral/testbench)
- [Laravel Testing](https://laravel.com/docs/testing)
