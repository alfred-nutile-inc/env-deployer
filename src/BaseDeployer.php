<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 6/2/15
 * Time: 8:25 PM
 */

namespace AlfredNutileInc\EnvDeployer;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class BaseDeployer {

    protected $filePath;
    protected $env = [];
    protected $env_name = '.env';
    protected $filesystem;

    public function checkForFile($path = false)
    {
        $path = ($path) ? $path : $this->getFilePath();

        if(!$this->getFilesystem()->exists($path))
            throw new \Exception(sprintf("No file found at -> %s",
                $path));
    }

    public function setFilePath($filePath = false)
    {
        if($filePath == false)
            $filePath = base_path();

        $this->filePath = $filePath;
    }

    public function getFilePath()
    {
        if($this->filePath == null)
            $this->setFilePath();

        return $this->filePath;
    }

    public function getEnvName()
    {
        return $this->env_name;
    }

    public function setEnvName($env_name)
    {
        $this->env_name = $env_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilesystem()
    {
        if($this->filesystem == null)
            $this->setFilesystem();
        return $this->filesystem;
    }

    /**
     * @param mixed $filesystem
     */
    public function setFilesystem($filesystem = null)
    {
        if($filesystem == null)
            $filesystem = new Filesystem();
        $this->filesystem = $filesystem;
    }

    protected function makeEnvArrayFromFile($path_with_name = false)
    {

        $path_with_name = ($path_with_name) ? $path_with_name : $this->filePath;

        /**
         * Thanks to Dotenv library by vlucas
         */
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($path_with_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    private function loadEnvFromFile()
    {
        $this->setFilePath(
            base_path($this->env_name));

        $this->checkForFile();

        $this->env = $this->makeEnvArrayFromFile();

        return $this->env;
    }

    public function getEnv()
    {
        if(empty($this->env))
            $this->setEnv();
        return $this->env;
    }

    public function setEnv($env = array())
    {
        if(empty($env))
            $env = $this->loadEnvFromFile();
        $this->env = $env;
    }

}