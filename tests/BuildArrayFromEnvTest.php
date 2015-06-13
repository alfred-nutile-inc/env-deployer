<?php
use AlfredNutileInc\EnvDeployer\BuildArrayFromEnv;
use AlfredNutileInc\EnvDeployer\Exceptions\TargetSettingNotFoundException;

class BuildArrayFromEnvTest extends \TestCase
{

    /**
     * @test
     */
    public function should_load_file()
    {
        $this->markTestSkipped("Just not needed on codeship");

        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $results = $ba->getEnv();

        $this->assertGreaterThan(5, $results);

        $this->assertEquals('#@dev=false', $results[2]);
    }

    /**
     * @test
     */
    public function should_reduce_to_target_values()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(["#@dev=bar", "#@stage=foo", "APP_ENV=local"]);
        $ba->buildOutNewEnvArray();

        $this->assertEquals('APP_ENV=\'bar\'', $ba->getTargetEnv()[0]);
        $this->assertCount(1, $ba->getTargetEnv());
    }

    /**
     * @test
     */
    public function should_include_default_settings_as_well_not_just_replaced_ones()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(["#@dev=bar", "#@stage=foo", "APP_ENV=local", "FOO_BAR='foo'"]);
        $ba->buildOutNewEnvArray();

        $this->assertEquals('APP_ENV=\'bar\'', $ba->getTargetEnv()[0]);
        $this->assertEquals('FOO_BAR=\'foo\'', $ba->getTargetEnv()[1]);
        $this->assertCount(2, $ba->getTargetEnv());
    }

    /**
     * @test
     */
    public function larger_set_prove_working()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(
            [
                "#@dev=bar",
                "#@stage=foo",
                "APP_ENV=local",
                "FOO_BAR='foo'",
                "#@dev=bar2",
                "#@stage=bar3",
                "FOO_BAR2=bar1",
            ]);
        $ba->buildOutNewEnvArray();

        $this->assertEquals('APP_ENV=\'bar\'', $ba->getTargetEnv()[0]);
        $this->assertEquals('FOO_BAR=\'foo\'', $ba->getTargetEnv()[1]);
        $this->assertEquals('FOO_BAR2=\'bar2\'', $ba->getTargetEnv()[2]);
        $this->assertCount(3, $ba->getTargetEnv());
    }

    /**
     * @test
     */
    public function larger_set_prove_working_stage()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('stage');
        $ba->setEnv(
            [
                "#@dev=bar",
                "#@stage=foo",
                "APP_ENV=local",
                "FOO_BAR='foo'",
                "#@dev=bar2",
                "#@stage=bar3",
                "FOO_BAR2=bar1",
            ]);
        $ba->buildOutNewEnvArray();

        $this->assertEquals('APP_ENV=\'foo\'', $ba->getTargetEnv()[0]);
        $this->assertEquals('FOO_BAR=\'foo\'', $ba->getTargetEnv()[1]);
        $this->assertEquals('FOO_BAR2=\'bar3\'', $ba->getTargetEnv()[2]);
        $this->assertCount(3, $ba->getTargetEnv());
    }

    /**
     * @test
     */
    public function should_return_integer()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(["#@dev=1", "#@stage=2", "FOO_BAR=5"]);
        $ba->buildOutNewEnvArray();

        $this->assertEquals('FOO_BAR=1', $ba->getTargetEnv()[0]);
        $this->assertCount(1, $ba->getTargetEnv());
    }

    /**
     * @test
     * @expectedException \AlfredNutileInc\EnvDeployer\Exceptions\TargetSettingNotFoundException
     */
    public function should_fail_if_no_target_found_under_token()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(["#@dev=bar"]);
        $ba->buildOutNewEnvArray();
    }

    /**
     * @test
     * @expectedException \AlfredNutileInc\EnvDeployer\Exceptions\TokenMissingValueException
     */
    public function should_fail_token_has_no_value()
    {
        $ba = (new BuildArrayFromEnv())->setTarget('dev');
        $ba->setEnv(["#@dev", "#@stage=foo", "APP_ENV=local"]);
        $ba->buildOutNewEnvArray();
    }

}