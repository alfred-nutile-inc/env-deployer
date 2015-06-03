<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 6/2/15
 * Time: 8:25 PM
 */

namespace AlfredNutileInc\EnvDeployer;


/**
 * Class PullEnv
 * @package AlfredNutileInc\EnvDeployer
 * Takes the Array from the command and
 * builds out the merged array to make into
 * the .env
 */
class PullEnv extends BaseDeployer {


    protected $source;
    protected $path = '/tmp/';
    protected $local_env_name = '.env';
    protected $local_env;
    protected $merged_env;
    protected $remote_env;
    protected $target_env;

    /**
     * Does not include file name
     * /home/forge/app/
     * not ending forward slash
     * @var
     */
    protected $full_local_path;

    public function buildOutMergedEnv()
    {
        $this->loadLocalEnv();
    }

    public function loadRemoteEnv($path, $filename)
    {
        $this->setFilePath($path);
        $this->checkForFile($path . $filename);
        $this->remote_env = $this->makeEnvArrayFromFile($path . $filename);
    }

    public function loadLocalEnv()
    {
        $this->checkForFile(
            $this->getFullLocalPath() . $this->local_env_name
        );

        $this->setLocalEnv(
            $this->makeEnvArrayFromFile(
                $this->getFullLocalPath() . $this->local_env_name));
    }

    /**
     * Make Merged Local from Remote and Local merged
     * 1) Take Remote
     * 2) Take Local
     * 3) Iterate over Remote
     * 4) For each Remote put into Local as #@env and Key=Value if local does not have it
     * 5) For each Local keep in local plus with the above added
     */
    public function mergeRemoteAndLocal()
    {
        $this->setMergedEnv([]);

        /**
         * Add while loop on $this->getRemoteEnv() > 0 since I will remove those
         * before moving onto appending Merged with Local remaining items
         */
        foreach($this->getRemoteEnv() as $key => $value)
        {
            $remote_key = $this->getValueBeforeEqualSign($value);

            if($local = $this->inLocal($remote_key))
            {
                //dd($local);
                //Found it
                //Does local have #@ happening before this to consider
                //Add #@ and update existing #@ if it is there
                //Unset all from local to clean it up including one found and #@ before it
            }

            //Local does not have it but now need to add it to target with #@
            //Then unset Remote and Local as needed

            //When done take remaining local and merge to the end of merged
        }
    }

    public function inLocal($key_from_remote_env)
    {
        foreach($this->getLocalEnv() as $key => $value)
        {
            $key_from_local_env = $this->getValueBeforeEqualSign($value);

            if($key_from_remote_env == $key_from_local_env)
            {
                return ['value' => $value, 'key' => $key];
            }
        }
    }

    protected function getFullLocalPath()
    {
        if($this->full_local_path == null)
            $this->setFullLocalPath();

        return $this->full_local_path;
    }

    public function setFullLocalPath($full_local_path = false)
    {
        if($full_local_path == false)
            $full_local_path = base_path();

        $this->full_local_path = $full_local_path;
        return $this;
    }

    public function getLocalEnvName()
    {
        return $this->local_env_name;
    }

    public function setLocalEnvName($local_env_name)
    {
        $this->local_env_name = $local_env_name;
        return $this;
    }

    public function getLocalEnv()
    {
        return $this->local_env;
    }

    public function setLocalEnv($local_env)
    {
        $this->local_env = $local_env;
    }

    public function getMergedEnv()
    {
        return $this->merged_env;
    }

    public function setMergedEnv($merged_env)
    {
        $this->merged_env = $merged_env;
    }

    public function getRemoteEnv()
    {
        return $this->remote_env;
    }

    public function setRemoteEnv($remote_env)
    {
        $this->remote_env = $remote_env;
    }

    public function getTargetEnv()
    {
        return $this->target_env;
    }

    public function setTargetEnv($target_env)
    {
        $this->target_env = $target_env;
    }

    private function getValueBeforeEqualSign($value)
    {
        return substr($value, 0, stripos($value, '='));
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

}