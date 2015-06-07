<?php

use AlfredNutileInc\EnvDeployer\EnvDeployerMakeExampleCommand;

/**
 * Class EnvDeployerMakeExampleTest
 */
class EnvDeployerMakeExampleTest extends \TestCase
{

    public function __construct()
    {
        parent::__construct();
        $this->m = Mockery::mock('AlfredNutileInc\EnvDeployer\BuildArrayFromEnv');
    }

    /**
     * @test
     */
    public function should_make_correct_example_env_array()
    {
        $this->m->shouldReceive('setFilePath');

        $this->m->shouldReceive('buildOutNewEnvArray');

        $this->m->shouldReceive('getEnv')
            ->andReturn([
                'APP_ENV=local',
                '# Database',
                'DB_HOST=localhost',
                'DB_DATABASE=local_db',
                'DB_USERNAME=homestead',
                '# DB_Password=',
                'DB_PASSWORD=secret',
            ]);

        $makeExampleCommand = new EnvDeployerMakeExampleCommand($this->m);
        $exampleEnvArray = $makeExampleCommand->makeExampleEnvArray('');

        $expected = [
            'APP_ENV=',
            '# Database',
            'DB_HOST=',
            'DB_DATABASE=',
            'DB_USERNAME=',
            '# DB_Password=',
            'DB_PASSWORD=',
        ];

        $this->assertEquals($expected, $exampleEnvArray);
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
