<?php
use AlfredNutileInc\EnvDeployer\SharingEnv;

/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 6/12/15
 * Time: 9:29 PM
 */

class SharingEnvTest extends TestCase {


    /**
     * @test
     */
    public function should_have_destination()
    {
        $share = (new SharingEnv())->setTarget('share');

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