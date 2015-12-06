<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 5/29/15
 * Time: 3:00 PM
 */

namespace AlfredNutileInc\EnvDeployer;


use AlfredNutileInc\EnvDeployer\Exceptions\TargetSettingNotFoundException;
use AlfredNutileInc\EnvDeployer\Exceptions\TokenMissingValueException;
use Illuminate\Support\Facades\File;

class BuildArrayFromEnv extends BaseDeployer
{

    protected $target;
    protected $target_env = [];
    protected $found_targets_originals = [];

    public function __construct($target = 'dev')
    {
        $this->target = $target;
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

    public function buildOutNewEnvArray()
    {
        $this->getEnv();
        $this->setTargetValues();
    }

    protected function setTargetValues()
    {
        while(count($this->env) > 0)
        {
            foreach($this->env as $key => $value)
            {
                if($this->rowIsToken($value) !== false)
                {
                    unset($this->env[$key]);
                    $this->findValueForTokenUnsetAsNeeded($key, $value);
                }
                elseif($this->rowIsATokenButNotNeeded($value))
                {
                    unset($this->env[$key]);
                }
                elseif($this->notAlreadyReplacedButHasNoToken($value))
                {
                    unset($this->env[$key]);
                    $this->setTargetEnv($value);
                }
                else
                {
                    unset($this->env[$key]);
                }
            }
        }
    }

    protected function swapTokenIntoTarget($token, $target)
    {
        $value_from_token           = $this->extractValueFromToken($token);
        $target_key_value_removed   = $this->extractKeyFromTarget($target);
        if(is_numeric($value_from_token))
            return sprintf("%s=%d", $target_key_value_removed, $value_from_token);

        return sprintf("%s=%s", $target_key_value_removed, $value_from_token);
    }

    protected function stripStartingTags($value)
    {
        return substr($value, 2);
    }

    private function getNextPossibleTarget($related_token)
    {
        $target_setting = false;

        foreach($this->env as $related_key => $related_value)
        {
            if(
                $this->notRelatedToken($related_value, $related_token)
                ||
                $this->notAToken($related_value))
            {
                $target_setting = $related_value;
                /**
                 * @NOTE ideally the unset would make this method not need
                 * but right now it is not working
                 */
                $this->setFoundTargetsOriginals($related_value);
                unset($this->env[$related_key]);
                break;
            }
        }

        return $target_setting;
    }

    private function findValueForTokenUnsetAsNeeded($key, $token_found)
    {
        $token              = $this->stripStartingTags($token_found);

        $target_setting     = $this->getNextPossibleTarget($token_found);

        if($target_setting == false)
        {
            throw new TargetSettingNotFoundException(
                sprintf("Could not find a target for the %s token", $token));
        }
        else
        {
            $swapped = $this->swapTokenIntoTarget($token, $target_setting);
            $this->setTargetEnv($swapped);
        }
    }

    private function rowIsAToken($value)
    {
        return strpos($value, '#@');
    }

    private function rowIsToken($value)
    {
        return strpos($value, '#@' . $this->target);
    }


    public function getTargetEnv()
    {
        return $this->target_env;
    }

    public function setTargetEnv($target_env)
    {
        $this->target_env[] = $target_env;
    }


    private function extractValueFromToken($token)
    {
        if(strpos($token, '=') === false)
            throw new TokenMissingValueException(sprintf("Token %s missing value", $token));

        $value = substr($token, stripos($token, '=') +1);
        return $value;
    }

    private function extractKeyFromTarget($target)
    {
        $subString = substr($target, 0, strpos($target, '='));
        return $subString;
    }



    private function notRelatedToken($related_value, $related_token)
    {
        return ( strpos($related_value, '#@') === false && $related_value != $related_token );
    }

    private function notAToken($related_value)
    {
        return strpos($related_value, '#@') === false;
    }

    private function rowIsATokenButNotNeeded($value)
    {
        return $this->rowIsAToken($value) !== false;
    }

    private function notAlreadyReplacedButHasNoToken($value)
    {
        return !in_array($value, $this->getFoundTargetsOriginals());
    }

    private function getFoundTargetsOriginals()
    {
        return $this->found_targets_originals;
    }

    public function setFoundTargetsOriginals($found_targets_originals)
    {
        $this->found_targets_originals[] = $found_targets_originals;
    }
}