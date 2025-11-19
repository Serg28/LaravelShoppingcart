# Руководство по обновлению до Laravel 11 и 12

Этот документ содержит инструкции по обновлению пакета LaravelShoppingcart для совместимости с Laravel 11 и 12.

## Требования

- **PHP**: >= 8.2
- **Laravel**: 9.x, 10.x, 11.x или 12.x
- **Carbon**: >= 2.0
- **PHPUnit**: >= 10.0 (для разработки)

## Изменения в зависимостях

### Обновленные требования

1. **PHP 8.2+**: Минимальная версия PHP повышена до 8.2 для соответствия требованиям Laravel 11 и 12.

2. **Laravel Framework**: Добавлена поддержка Laravel 11.x и 12.x:
   ```json
   "laravel/framework": "^9.0||^10.0||^11.0||^12.0"
   ```

3. **Carbon**: Обновлена поддержка до версии 3.x:
   ```json
   "nesbot/carbon": "^2.0||^3.0"
   ```

4. **PHPUnit**: Обновлен до версий 10.x и 11.x:
   ```json
   "phpunit/phpunit": "^10.0||^11.0"
   ```

5. **Orchestra Testbench**: Обновлен для поддержки новых версий Laravel:
   ```json
   "orchestra/testbench": "^7.0||^8.0||^9.0||^10.0"
   ```

## Установка

### Для новых проектов

```bash
composer require bumbummen99/shoppingcart
```

### Обновление существующих проектов

1. Обновите зависимости в вашем `composer.json`:
   ```bash
   composer update bumbummen99/shoppingcart
   ```

2. Если вы используете PHP < 8.2, сначала обновите PHP:
   ```bash
   # Для Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install php8.2
   ```

3. Очистите кэш конфигурации:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Публикация конфигурации и миграций

Если вы еще не опубликовали конфигурационный файл:

```bash
php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="config"
```

Если вы еще не опубликовали миграции:

```bash
php artisan vendor:publish --provider="Gloudemans\Shoppingcart\ShoppingcartServiceProvider" --tag="migrations"
php artisan migrate
```

## Совместимость с Laravel 11

Laravel 11 внес несколько изменений в структуру приложения:

### Service Provider

Пакет автоматически регистрируется через Package Discovery. Если вы используете Laravel 11 с новой структурой `bootstrap/providers.php`, пакет будет работать без дополнительных настроек.

### Middleware

Если вы используете middleware для управления корзиной, убедитесь, что они зарегистрированы в `bootstrap/app.php` (Laravel 11) или `app/Http/Kernel.php` (Laravel 10 и ниже).

## Совместимость с Laravel 12

Laravel 12 полностью поддерживается. Все функции пакета работают без изменений.

## Тестирование

После обновления рекомендуется запустить тесты:

```bash
composer install --dev
vendor/bin/phpunit
```

## Известные проблемы

### PostgreSQL и нулевые байты

Если вы используете PostgreSQL и обновляетесь с версии < 4.2.0, рекомендуется очистить таблицу корзины:

```sql
TRUNCATE TABLE shoppingcart;
```

Это связано с изменением кодирования контента корзины в base64 для решения проблемы с нулевыми байтами.

## Обратная совместимость

Пакет сохраняет полную обратную совместимость с Laravel 9 и 10. Все существующие API и функции работают без изменений.

## Поддержка

Если у вас возникли проблемы с обновлением, создайте issue на GitHub:
https://github.com/bumbummen99/LaravelShoppingcart/issues

## Дополнительные ресурсы

- [Документация Laravel 11](https://laravel.com/docs/11.x)
- [Документация Laravel 12](https://laravel.com/docs/12.x)
- [Руководство по обновлению Laravel](https://laravel.com/docs/11.x/upgrade)
