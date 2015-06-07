<?php namespace AlfredNutileInc\EnvDeployer;

use AlfredNutileInc\EnvDeployer\Exceptions\FileNotFound;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class EnvDeployerMakeExampleCommand
 * @package AlfredNutileInc\EnvDeployer
 */
class EnvDeployerMakeExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envdeployer:make-example {--from=<base_path>/.env} {--to=<base_path>/.env.example}';

    protected $name = 'envdeployer:make-example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an .env.example from the current .env';

    /**
     * @var BuildArrayFromEnv
     */
    protected $ba;

    /**
     * Create a new command instance.
     */
    public function __construct(BuildArrayFromEnv $ba)
    {
        parent::__construct();
        $this->ba = $ba;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        list($from, $to) = $this->getFromToFilePaths();

        $exampleSettings = $this->makeExampleEnvArray($from);

        file_put_contents($to, implode("\n", $exampleSettings));

        $this->info('Example has been updated: ' . $to);
    }

    public function makeExampleEnvArray($envPath)
    {
        $this->ba->setFilePath($envPath);
        $this->ba->buildOutNewEnvArray();

        $envSettings = $this->ba->getEnv();
        $exampleSettings = [];

        foreach ($envSettings as $setting) {
            if ($this->isLineAComment($setting)) {
                $exampleSettings[] = $setting;
            } else {
                list($name, $value) = $this->splitSettingStringIntoParts($setting);
                $exampleSettings[] = $name . '=';
            }
        }

        return $exampleSettings;
    }

    /**
     * Get the console command options.
     * For compatibility with L5.0
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['from', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_REQUIRED, 'The file path for the source .env', base_path('.env')],
            ['to', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_REQUIRED, 'The file path for the destination .env.example', base_path('.env.example')],
        ];
    }

    /**
     * Get the file paths for the source .env and destination .env.example.
     *
     * @return array
     *
     * @throws FileNotFound
     */
    private function getFromToFilePaths()
    {
        $from = $this->option('from');

        if (empty($from)) {
            $from = base_path('.env');
        }

        if (!file_exists($from)) {
            throw new FileNotFound($from . ' - does not exist');
        }

        $to = $this->option('to');

        if (empty($to)) {
            $to = base_path('.env.example');
        }

        $parentDir = dirname($to);

        if (!is_dir($parentDir)) {
            throw new FileNotFound($parentDir . ' - does not exist');
        }

        return [$from, $to];
    }

    /**
     * Split the setting string into an array of variable name and value.
     * Thanks to https://github.com/vlucas/phpdotenv/blob/master/src/Loader.php
     *
     * @param string $setting
     *
     * @return array
     */
    private function splitSettingStringIntoParts($setting)
    {
        $name = '';
        $value = '';
        if (strpos($setting, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $setting, 2));
        }
        return array($name, $value);
    }

    /**
     * Determine if a line is a comment.
     *
     * @param $line
     *
     * @return bool
     */
    private function isLineAComment($line)
    {
        return strpos($line, '#') === 0;
    }
}

