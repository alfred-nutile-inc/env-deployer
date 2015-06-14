<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 6/2/15
 * Time: 8:25 PM
 */

namespace AlfredNutileInc\EnvDeployer;


use AlfredNutileInc\EnvDeployer\Exceptions\ConfigMissingEnvironmentException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class BaseDeployer {

    protected $filePath;
    protected $target;
    protected $env = [];
    protected $env_name = '.env';
    protected $filesystem;
    protected $config;
    protected $target_directory;

    public function checkForFile($path = false)
    {
        $path = ($path) ? $path : $this->filePath;

        if(!$this->getFilesystem()->exists($path))
            throw new \Exception(sprintf("No file found at -> %s",
                $path));
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFilePath()
    {
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

    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    public function getTarget()
    {
        return $this->target;
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

    public function loadEnvFromFile()
    {
        if($this->filePath == null)
            $this->setFilePath(base_path($this->env_name));

        $this->checkForFile();

        $this->env = $this->makeEnvArrayFromFile();

        return $this;
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

    public function loadTargetSshFromConfig()
    {
        $config = $this->getFromConfig('envdeployer.connections.' . $this->target);

        if(empty($config))
            throw new ConfigMissingEnvironmentException(sprintf("Please make sure you have %s set in the config file", $this->target));

        $this->target_directory = $config;
    }

    public function getFromConfig($key)
    {
        if($this->config == null)
            $this->setConfig();

        return $this->config->get($key);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(Repository $repository = null)
    {
        if($repository == null)
            $repository = App::make('Illuminate\Contracts\Config\Repository');

        $this->config = $repository;

        return $this;
    }

    public function getTargetDirectory()
    {
        return $this->target_directory;
    }

    public function setTargetDirectory($target_directory)
    {
        $this->target_directory = $target_directory;
    }
}