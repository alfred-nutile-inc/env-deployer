<?php
use AlfredNutileInc\EnvDeployer\PullEnv;

if(!function_exists('base_path'))
{
    function base_path($arg = '')
    {
        return __DIR__ . '/../' . $arg;
    }
}
class PullEnvTest extends \TestCase {


    /**
     * @test
     */
    public function should_load_remote_env()
    {
        $pull = (new PullEnv());

        $pull->loadRemoteEnv(__DIR__ . '/fixtures/', '.remote_env');

        $this->assertNotNull($pull->getRemoteEnv());
        $this->assertCount(3, $pull->getRemoteEnv());

    }

    /**
     * @test
     */
    public function should_load_local_env()
    {
        $pull = (new PullEnv())
            ->setLocalEnvName('.local_env')
            ->setFullLocalPath(__DIR__ . '/fixtures/');

        $pull->loadLocalEnv();

        $this->assertNotNull($pull->getLocalEnv());
        $this->assertCount(8, $pull->getLocalEnv());
    }

    /**
     * @test
     */
    public function set_merged_env()
    {
        $pull = (new PullEnv())
            ->setLocalEnvName('.local_env')
            ->setFullLocalPath(__DIR__ . '/fixtures/');

        $pull->loadRemoteEnv(__DIR__ . '/fixtures/', '.remote_env');
        $pull->loadLocalEnv();

        $pull->mergeRemoteAndLocal();

        $this->assertNotNull($pull->getMergedEnv());

    }
}