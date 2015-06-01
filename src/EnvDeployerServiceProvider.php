<?php namespace AlfredNutileInc\EnvDeployer;


class EnvDeployerServiceProvider extends \Illuminate\Support\ServiceProvider
{

    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/envdeployer.php' => config_path('envdeployer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app['envdeployer.push'] = $this->app->share(
            function ($app) {
                return new EnvDeployerCommand();
            }
        );

        $this->commands('envdeployer.push');
    }

    public function provides()
    {
        return ['envdeployer.push'];
    }
}