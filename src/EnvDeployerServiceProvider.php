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
        $this->app->singleton('envdeployer.push', function($app) {
            return new EnvDeployerCommand();
        });

        $this->app->singleton('envdeployer.make-example', function($app) {
            return new EnvDeployerMakeExampleCommand(new BuildArrayFromEnv());
        });

        $this->commands('envdeployer.push', 'envdeployer.make-example');
    }

    public function provides()
    {
        return ['envdeployer.push', 'envdeployer.make-example'];
    }
}
