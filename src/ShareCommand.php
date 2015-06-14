<?php namespace AlfredNutileInc\EnvDeployer;

use AlfredNutileInc\EnvDeployer\Exceptions\ConfigMissingEnvironmentException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ShareCommand extends Command
{
    protected $name = 'envdeployer:share';
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envdeployer:share';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share Env file by sending it to server for other user to get and delete';

    /**
     * The target name dev, stage, prod
     */
    protected $target;

    /**
     * @var BuildArrayFromEnv
     */
    protected $ba;

    protected $temp_path = '/tmp/';

    protected $config = [
        'host'              => false,
        'user'              => false,
        'rootEnvDirectory'  =>  false
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->ba = new BuildArrayFromEnv();


        $this->ba->setTarget($this->target)->buildOutNewEnvArray();

        $this->writeTemp();

        $this->loadTargetSshFromConfig();

        $this->sendFileOver();

        $this->removeTmp();

    }

    protected function writeTemp()
    {
        File::put("/tmp/env_temp", implode("\n", $this->ba->getTargetEnv()));
    }

    protected function removeTmp()
    {
        if(File::exists("/tmp/env_temp"))
            File::delete("/tmp/env_temp");
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    private function loadTargetSshFromConfig()
    {
        $config = Config::get('envdeployer.connections.' . $this->target);
        if(empty($config))
            throw new ConfigMissingEnvironmentException(sprintf("Please make sure you have %s set in the config file", $this->target));

        $this->config = $config;
    }

    private function sendFileOver()
    {
        $this->guardAgainstInvalidConnectionParameters();

        $scpCommand = "scp ". $this->setPort() . $this->sourceFile() .$this->userName().$this->config['host'].":".$this->config['rootEnvDirectory'].".env";

        $this->info('start scp on host '.$this->config['host'].' in directory '.$this->config['rootEnvDirectory']);

        $process = $this->executeCommand($scpCommand);

        if($process->getExitCode() != 0)
        {
            $this->error(sprintf("\nError during the copy %s", $process->getErrorOutput()));
            exit;
        }
        else
        {
            $this->info(sprintf("\nDone copying file to target host %s", $this->config['host']));
        }


    }

    protected function executeCommand($command)
    {
        $output = $this->output;
        $proccess = new Process($command);
        $output->progressStart(100);

        $proccess->setTimeout(null)->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write("\n" . $buffer);
                $output->progressAdvance();
            } else {
                $output->write("\n" . $buffer . "\n");
                $output->progressAdvance();
            }
        });

        if($proccess->getExitCode() == 0)
            $output->progressFinish();


        return $proccess;
    }

    protected function guardAgainstInvalidConnectionParameters()
    {
        if ($this->config['host'] == '')
        {
            throw new Exception('Hostname is required');
        }

        if ($this->config['rootEnvDirectory'] == '')
        {
            throw new Exception('Root Target Folder is required');
        }

        if (substr($this->config['rootEnvDirectory'], -1) !== '/')
            throw new Exception('Root Env Directory must end in forward slash /');
    }

    private function sourceFile()
    {
        return "/tmp/env_temp ";
    }

    private function setPort()
    {
        return ($this->config['port'] == '' ? '-P22' : '-P' . $this->config['port']) . ' ';
    }

    private function userName()
    {
        return ($this->config['user'] == '' ? '' : $this->config['user'] . '@');
    }
}
