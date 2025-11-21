<?php

namespace Linecore\Shoppingcart;

use Illuminate\Auth\Events\Logout;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class ShoppingcartServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cart', function ($app) {
            return new Cart($app['session'], $app['events']);
        });

        $config = __DIR__.'/Config/cart.php';
        $this->mergeConfigFrom($config, 'cart');

        $this->publishes([__DIR__.'/Config/cart.php' => config_path('cart.php')], 'config');

        $this->app['events']->listen(Logout::class, function () {
            if ($this->app['config']->get('cart.destroy_on_logout')) {
                $this->app->make(SessionManager::class)->forget('cart');
            }
        });

        $this->publishes([
            realpath(__DIR__.'/Database/migrations') => $this->app->databasePath().'/migrations',
        ], 'migrations');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Регистрируем алиас фасада Cart
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Cart', \Linecore\Shoppingcart\Facades\Cart::class);
    }
}
