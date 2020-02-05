<?php
/**
 * @copyright (c) 2019
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// 1. Fix the controller's relation fields, hasMany and hasOne, belongsTo and belongsToMany

3. Add the Resource to the Lightning directory, including fields, relations, filters and actions
4. Create groups and permissions based on the models provided in the config file.

optional Add a Repository to the Lightning directory, base

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace Simianbv\JsonSchema\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Simianbv\JsonSchema\Console\Generators\ModelInspector;
use Symfony\Component\Yaml\Yaml;

/**
 * @class   MakeSchemaFromModel
 * @package App\Console\Commands
 */
class MakeSchemaFromModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:json-model {model}';

    /**
     * @var string
     */
    private $log_path = "";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a yaml file ( or create a direct resource ) from a model given';

    /**
     * @var array
     */
    protected $files_generated = [];

    /**
     * @var int
     */
    protected static $PAD_LENGTH = 30;

    /**
     * @var ModelInspector
     */
    private $modelInspector;

    /**
     * Create a new command instance.
     *
     * @param ModelInspector $modelInspector
     */
    public function __construct(
        ModelInspector $modelInspector
    ) {
        parent::__construct();
        $this->modelInspector = $modelInspector;
        $this->modelInspector->setCallee($this);
    }


    /**
     * Parse the Yaml input file
     *
     * Parse the Yaml file, check if the file exists and return whatever it was able to parse.
     *
     * @param string $file
     *
     * @return mixed
     * @throws Exception
     */
    private function parseYaml(string $file)
    {
        $filePath = config('json-schema.yaml.path') . $file . '.yml';
        if (!File::exists($filePath)) {
            throw new Exception("Unable to process the file, the file does not exist. We've looked at " . $filePath);
        }
        $yaml = File::get($filePath);
        return Yaml::parse($yaml);
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        // initial install
        $this->createAllDirectories();

        $modelData = $this->modelInspector->process($this->argument('model'));
        // @todo: update as soon as it's ready
    }

    /**
     * A helper function to check if all the required directories exist.
     * @return void
     */
    private function createAllDirectories(): void
    {
        if (config('json-schema.logging')) {
            $this->log_path = config('json-schema.log_path');

            if (!File::isDirectory($this->getLogPath())) {
                $this->line("<fg=magenta>" . $this->getLogPath() . "</>\n");
                File::makeDirectory($this->getLogPath(), 0755, true);
            }
        }

        if (!File::isDirectory(config('json-schema.yaml.path'))) {
            $this->line("<fg=magenta>" . config('json-schema.yaml.path') . "</>\n");
            File::makeDirectory(config('json-schema.yaml.path'), 0755, true);
        }

        if (!File::isDirectory(config('json-schema.resources.path'))) {
            $this->line("<fg=magenta>" . config('json-schema.resources.path') . "</>\n");
            File::makeDirectory(config('json-schema.resources.path'), 0755, true);
        }
    }

    /**
     * @return string
     */
    private function getLogPath()
    {
        return $this->log_path;
    }

    /**
     * Trim the value from all slashes
     *
     * @param string $value
     * @param string $trim defaults to \
     *
     * @return string
     */
    private function trim(string $value, string $trim = '\\')
    {
        return rtrim(trim($value, $trim), $trim);
    }

    /**
     * Return the namespace if the namespace is set, otherwise, return an empty string
     *
     * @param string $ns
     *
     * @return string
     */
    private function ns(string $ns): string
    {
        return strlen($ns) > 0 ? $ns . '\\' : '';
    }

    /**
     * @param string $ns
     *
     * @return string
     */
    private function dir(string $ns): string
    {
        return strlen($ns) > 0 ? $ns . '/' : '';
    }

    /**
     * Revert the creation of all the files, in case something went wrong
     * @return void
     */
    private function revert()
    {
        $this->warn("Reverting back, deleting all the created files");
        foreach ($this->files_generated as $file) {
            $this->warn("Deleting file: " . $file);
        }
        File::delete($this->files_generated);
    }
}
