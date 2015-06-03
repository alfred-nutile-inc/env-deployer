[![Build Status](https://travis-ci.org/alfred-nutile-inc/env-deployer.svg)](https://travis-ci.org/alfred-nutile-inc/env-deployer)

# Env Deployer

See the [video](http://youtu.be/ppEzRgGSdFw)

![envdeploy](https://dl.dropboxusercontent.com/s/6q8y2jonnv08sef/envdeployer.png?dl=0)


This will take your local environment and deploy it to the selected server

Your config file will have the list of servers

For example say your .env looks like this

The new Laravel tail library `spatie/laravel-tail` inspired and helped this a ton


~~~
#@dev=dev
#@stage=stage
APP_ENV=local

#@dev=dev_db
#@stage=stage_db
DATABASE_NAME=local_db
~~~

If you run the command

~~~
php artisan envdeployer:push dev
~~~

This will send you local to dev replacing the values as needed.

~~~
APP_ENV=dev
DATABASE_NAME=dev_db
~~~

This makes it super easy for local developers to merge their env to
the different servers while at time keep a local .env and .env.example

~~~
php artisan envdeployer:make-example
~~~

Would then setup example with random values

~~~
php artisan envdeployer:share
~~~

Would place it on the config setting share path for the team member to write

~~~
php artisan envdeployer:share get
~~~

To get and pull that down as their env settings.

Lastly

~~~
php artisan envdeployer:get_and_merge dev
~~~

Would get dev and merge it into your local setting `#@dev=foo` as needed and adding values you are missing

## Install

You can install the package via composer:

``` bash
composer require 'alfred-nutile-inc/env-deployer:dev-master'
```
You must install this service provider:
```php
// config/app.php

'providers' => [
    ...
    'AlfredNutileInc\EnvDeployer\EnvDeployerServiceProvider',
    ...
];
```

Now to setup your config:

``` bash
php artisan vendor:publish --provider="AlfredNutileInc\EnvDeployer\EnvDeployerServiceProvider"
```
A file named ``envdeployer.php`` will be created in the config directory. The options you can set in the file should be self-explanatory.
```php

<?php

return [

    'connections' => [

        /*
         * The environment name.
         */
        'dev' => [

            /*
             * The hostname to send the env file to
             */
            'host'  => 'example_target.dev',

            /*
             * The username to be used when connecting to the server where the logs are located
             */
            'user' => 'vagrant',

            /*
             * The full path to the directory where the .env is located MUST end in /
             */
            'rootEnvDirectory' => '/home/vagrant/mysite/',

            'port' => 2222
        ],
    ],
];

```



# RoadMap

Immediate Todo Items
Make it a true laravel 5 library with config settings


1) send file to target environment

2) get file from target and merge into local

3) share with developers
