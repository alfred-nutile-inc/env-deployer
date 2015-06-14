<?php
use AlfredNutileInc\EnvDeployer\SharingEnv;
use EnvDeployer\Tests\Config;


class SharingEnvTest extends TestCase {

    protected $basePath;

    public function setUp()
    {
        $this->basePath = __DIR__ . '/.env';
    }

    /**
     * @test
     */
    public function should_load_local_env()
    {

        $path = (base_path() != null) ? base_path() : $this->basePath;

        $share = (new SharingEnv())->setFilePath($path)->loadEnvFromFile();

        $this->assertNotNull($share->getEnv());

        $this->assertCount(4, $share->getEnv());
    }

    /**
     * @test
     */
    public function should_have_destination()
    {
        $path = (base_path() != null) ? base_path() : $this->basePath;

        $share = (new SharingEnv())->setFilePath($path)->loadEnvFromFile();

        $share->setTarget('share');

        $share->setConfig(new Config());

        die($share->loadTargetSshFromConfig());


    }

    /**
     * @test
     */
    public function should_upload_to_drive()
    {

    }

    /**
     * @test
     */
    public function should_delete_on_get()
    {

    }

    /**
     * @test
     */
    public function should_get_on_get()
    {

    }
}