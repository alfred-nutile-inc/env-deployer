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
        'share' => [

            /*
             * The hostname to send the env file to for sharing
             */
            'host'  => 'example_target.dev',

            /*
             * The username to be used when connecting to the server where the logs are located
             */
            'user' => 'vagrant',

            /*
             * The full path to the directory where the .env is located MUST end in /
             */
            'rootEnvDirectory' => '/tmp/',

            'port' => 2222
        ],
    ],
];
