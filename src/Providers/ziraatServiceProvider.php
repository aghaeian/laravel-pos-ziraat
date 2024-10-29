<?php
 
namespace aghaeian\ziraat\Providers;

use Illuminate\Support\ServiceProvider;
 
/**
 *  ziraat service provider
 *
 * @author  aghaeian
 */
class ziraatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    
   
    public function boot()
    {
		
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'ziraat');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ziraat');
			
        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('/vendor/aghaeian/ziraat/assets'),
        ], 'iyzico');

    }
 
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {   
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {   
        //this will merge payment method
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'payment_methods'
        );

        // add menu inside configuration  
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'   
        );

    }
}
