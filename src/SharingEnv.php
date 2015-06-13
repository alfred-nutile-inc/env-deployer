<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 6/12/15
 * Time: 9:39 PM
 */

namespace AlfredNutileInc\EnvDeployer;


class SharingEnv extends BaseDeployer {

    public function __construct($target = 'share')
    {
        $this->target = $target;
    }

    public function handleSend()
    {

        //Get Source Path from Config
        //Get Destination Path from Config
        //Get File in memory
        //Send over and let the user know
    }

    public function setFileToSharedLocation()
    {

    }

    public function getFileFromSharedLocation()
    {

    }

    public function removeFileFromSharedLocation()
    {

    }


}