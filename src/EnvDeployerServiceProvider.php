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

        $this->app['envdeployer.make-example'] = $this->app->share(
            function ($app) {
                return new EnvDeployerMakeExampleCommand(new BuildArrayFromEnv());
            }
        );

        $this->app['envdeployer.share'] = $this->app->share(
            function ($app) {
                return new SharingEnv();
            }
        );

        $this->commands('envdeployer.push', 'envdeployer.make-example', 'envdeveloper.share');
    }

    public function provides()
    {
        return ['envdeployer.push', 'envdeployer.make-example'];
    }
}
