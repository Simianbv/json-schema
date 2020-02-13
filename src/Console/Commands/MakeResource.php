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
use Simianbv\JsonSchema\Console\Generators\Controller;
use Simianbv\JsonSchema\Console\Generators\Migration;
use Simianbv\JsonSchema\Console\Generators\Model;
use Simianbv\JsonSchema\Console\Generators\Overview;
use Simianbv\JsonSchema\Console\Generators\Resource;
use Symfony\Component\Yaml\Yaml;

/**
 * @class   MakeResource
 * @package App\Console\Commands
 */
class MakeResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:json-schema {name}';

    /**
     * @var string
     */
    private $log_path = "";

    /**
     * A string containing all the routes you need to add.
     * @var array
     */
    protected $routes = [];

    /**
     * A string containing all the routes you need to add.
     * @var array
     */
    protected $frontend_routes = [];

    /**
     * A string containing all the imports you need to add.
     * @var array
     */
    protected $frontend_imports = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full stack for the models defined in the yaml file, ie. 
    create a model, resource, repository and controller for each model defined';

    /**
     * @var Model
     */
    protected $modelGenerator;

    /**
     * @var Controller
     */
    protected $controllerGenerator;

    /**
     * @var Migration
     */
    protected $migrationGenerator;

    /**
     * @var Resource
     */
    protected $resourceGenerator;
    /**
     * @var Overview
     */
    private $overviewGenerator;
    /**
     * @var array
     */
    protected $files_generated = [];
    /**
     * @var int
     */
    protected static $PAD_LENGTH = 30;


    /**
     * Create a new command instance.
     *
     * @param Model      $modelGenerator
     * @param Controller $controllerGenerator
     * @param Migration  $migrationGenerator
     * @param Resource   $resourceGenerator
     * @param Overview   $overviewGenerator
     */
    public function __construct(
        Model $modelGenerator,
        Controller $controllerGenerator,
        Migration $migrationGenerator,
        Resource $resourceGenerator,
        Overview $overviewGenerator
    ) {
        parent::__construct();
        $this->modelGenerator = $modelGenerator;
        $this->controllerGenerator = $controllerGenerator;
        $this->migrationGenerator = $migrationGenerator;
        $this->resourceGenerator = $resourceGenerator;
        $this->overviewGenerator = $overviewGenerator;
        // notify the generators who's calling them
        $this->modelGenerator->setCallee($this);
        $this->controllerGenerator->setCallee($this);
        $this->migrationGenerator->setCallee($this);
        $this->resourceGenerator->setCallee($this);
        $this->overviewGenerator->setCallee($this);
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
                $this->line("<fg=magenta>log path location: " . $this->getLogPath() . "</>\n");
                File::makeDirectory($this->getLogPath(), 0755, true);
            }
        }

        if(!File::isDirectory(config('json-schema.overview.location'))){
            $this->line("<fg=magenta>creating directory: " . config('json-schema.overview.location') . "</>\n");
            File::makeDirectory(config('json-schema.overview.location'), 0755, true);
        }

        if (!File::isDirectory(config('json-schema.yaml.path'))) {
            $this->line("<fg=magenta>creating directory: " . config('json-schema.yaml.path') . "</>\n");
            File::makeDirectory(config('json-schema.yaml.path'), 0755, true);
        }

        if (!File::isDirectory(config('json-schema.resources.path'))) {
            $this->line("<fg=magenta>creating directory: " . config('json-schema.resources.path') . "</>\n");
            File::makeDirectory(config('json-schema.resources.path'), 0755, true);
        }
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

        $this->info("Processing the yaml file, please wait..");
        $data = $this->parseYaml($this->argument('name'));

        if (!$data) {
            $this->info("No data found, will not continue processing");
            return;
        }
        $idx = 1;
        // perform the creation in several passes, that way, we can create all the base models first and their corresponding
        // migrations, before moving on to the creation of controllers and the resources
        foreach ($data as $resourceName => $resource) {
            try {
                $this->createFirstPass($resourceName, $resource, $idx);
            } catch (Exception $e) {
                $this->revert();
                throw $e;
            }
            $idx++;
        }

        // on the second pass, create the controllers and resource objects
        foreach ($data as $resourceName => $resource) {
            try {
                $this->createSecondPass($resourceName, $resource, $idx);
            } catch (Exception $e) {
                $this->revert();
                throw $e;
            }
            $idx++;
        }

        try {
            $this->output();
        } catch (Exception $e) {
            $this->revert();
            throw $e;
        }
    }

    /**
     * Write the output to the console and optionally to a logfile as well.
     * @return void
     */
    private function output()
    {
        $lines = [
            "All files generated:\n\n",
        ];

        $lines += $this->files_generated;

        if (!empty($this->routes)) {
            $line = "\n ⚡️Copy over these routes to your web/api folder to make sure the controllers actually work and listen";
            $this->line("<fg=magenta>$line</>\n");
            $lines[] = $line;

            foreach ($this->routes as $route) {
                $this->line('<fg=yellow>' . $route . '</>');
                $lines[] = $route;
            }
        }

        if (!empty($this->frontend_routes)) {
            $line = "\n ⚡️Front-end ~ Copy over these imports at the top of your router.js file in the front-end to make sure the routes have the correct files";
            $this->line("<fg=magenta;bg=yellow>$line</>\n");
            $lines[] = $line;

            foreach ($this->frontend_imports as $import) {
                $this->line('<fg=yellow>' . $import . '</>');
                $lines[] = $import;
            }

            $line = "\n ⚡️Front-end ~ Copy over these routes to your router.js file in the front-end to make the links followable";
            $this->line("<fg=blue;bg=yellow>$line</>\n");
            $lines[] = $line;
            foreach ($this->frontend_routes as $route) {
                $this->line('<fg=yellow>' . $route . '</>');
                $lines[] = $route;
            }
        }

        if (config('json-schema.logging')) {
            file_put_contents($this->getLogPath() . date('Y-m-d_H_i') . "-" . $this->argument('name') . '.log', implode("\n", $lines));
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
     * @param     $resourceName
     * @param     $resource
     * @param int $index
     */
    private function createFirstPass($resourceName, $resource, $index = 0)
    {
        $model = ucfirst($this->trim($resourceName));
        $namespace = isset($resource['namespace']) ? $this->trim($resource['namespace']) : '';

        if ($this->isPivotOnly($resource)) {
            $this->createMigrationIfNotExisting($namespace, $model, $resource, $index, false);
        } else {
            $this->createModelIfNotExisting($namespace, $model, $resource, $index);
            $this->createMigrationIfNotExisting($namespace, $model, $resource, $index, true);
        }
    }

    /**
     * @param     $resourceName
     * @param     $resource
     * @param int $index
     */
    private function createSecondPass($resourceName, $resource, $index = 0)
    {
        $model = ucfirst($this->trim($resourceName));
        $namespace = isset($resource['namespace']) ? $this->trim($resource['namespace']) : '';

        if (!$this->isPivotOnly($resource)) {
            $this->createControllerIfNotExisting($namespace, $model, $resource);
            $this->createResourceIfNotExisting($namespace, $model, $resource);
            $this->createOverview($namespace, $model, $resource);
        }
    }

    /**
     * Returns true if the model provided should be a pivot only table.
     *
     * @param $resource
     *
     * @return bool
     */
    private function isPivotOnly($resource)
    {
        return isset($resource['pivot_only']) && $resource['pivot_only'] == true;
    }

    /**
     * Create the Overview for the given resource.
     *
     * @param $namespace
     * @param $model
     * @param $resource
     *
     * @return void
     */
    private function createOverview($namespace, $model, $resource)
    {
        $overviewFile = ucfirst(Str::slug(Str::snake(Str::plural($model))) . "-overview.vue");
        $detailFile = ucfirst(Str::slug(Str::snake($model)) . "-detail.vue");

        $overviewPath = base_path('resources/views/generated/' . $overviewFile);
        $detailPath = base_path('resources/views/generated/' . $detailFile);

        $this->info(str_pad("Creating Overview:", self::$PAD_LENGTH) . $overviewPath);
        $contents = $this->overviewGenerator->create($resource, $namespace, $model);


        file_put_contents($overviewPath, $contents['overview']);
        file_put_contents($detailPath, $contents['detail']);

        $this->files_generated[] = $overviewPath;
        $this->files_generated[] = $detailPath;
    }

    /**
     * Create the resource if the file does not exist yet.
     *
     * @param $namespace
     * @param $model
     * @param $resource
     *
     * @return void
     */
    private function createResourceIfNotExisting($namespace, $model, $resource)
    {
        $resourceClass = $model . 'Resource';
        $resourceDir = config('json-schema.resources.path') . '/' . $this->dir($namespace);
        $resourcePath = $resourceDir . $resourceClass . '.php';


        if (!File::isDirectory($resourceDir)) {
            File::makeDirectory($resourceDir, 0755, true);
        }

        //        if (!File::exists($resourcePath)) {
        $this->info(str_pad("Creating Resource:", self::$PAD_LENGTH) . $resourcePath);
        $resourceContent = $this->resourceGenerator->create($resource, $namespace, $model);
        file_put_contents($resourcePath, $resourceContent);
        $this->files_generated[] = $resourcePath;
        //        }
    }

    /**
     * Create the controller class for the resource if the controller doesn't exist yet.
     *
     * @param $namespace
     * @param $model
     * @param $resource
     *
     * @return void
     */
    private function createControllerIfNotExisting($namespace, $model, $resource)
    {
        $controllerClass = $model . 'Controller';
        $controllerPath = app_path('Http/Controllers/' . $this->dir($namespace) . $controllerClass) . '.php';

        if (!File::exists($controllerPath)) {
            if (!File::isDirectory(app_path('Http/Controllers/' . $this->dir($namespace)))) {
                File::makeDirectory(app_path('Http/Controllers/' . $this->dir($namespace)), 0755, true);
            }

            Artisan::call('make:request', ['name' => $this->ns($namespace) . 'Create' . $model . 'Request',]);
            Artisan::call('make:request', ['name' => $this->ns($namespace) . 'Update' . $model . 'Request',]);

            $this->info(str_pad("Creating Controller:", self::$PAD_LENGTH) . $controllerPath);
            $controllerContent = $this->controllerGenerator->create($resource, $namespace, $model);
            file_put_contents($controllerPath, $controllerContent);
            $this->files_generated[] = $controllerPath;
        }
    }

    /**
     *
     * Create the model, the migration and return the model once created.
     *
     * @param      $namespace
     * @param      $model
     * @param      $resource
     *
     * @param int  $index
     *
     * @param bool $forceMigration
     *
     * @return bool|void
     */
    private function createModelIfNotExisting($namespace, $model, $resource, $index = 0, $forceMigration = true)
    {
        $modelBasePath = trim(config('json-schema.models.path'), '/') . '/';
        if (!File::isDirectory(base_path($modelBasePath . $this->dir($namespace)))) {
            File::makeDirectory(base_path($modelBasePath . $this->dir($namespace)), 0755, true);
        }
        $modelPath = base_path($modelBasePath . $this->dir($namespace) . $model) . '.php';

        if (!File::exists($modelPath)) {
            $this->createMigrationIfNotExisting($namespace, $model, $resource, $index);

            $this->info(str_pad("Creating Model: ", self::$PAD_LENGTH) . $modelPath);
            $modelContent = $this->modelGenerator->create($resource, $namespace, $model);
            file_put_contents($modelPath, $modelContent);
            $this->files_generated[] = $modelPath;
        } else {
            if ($forceMigration) {
                $this->createMigrationIfNotExisting($namespace, $model, $resource, $index, $forceMigration);
            }
        }
    }

    /**
     * Create a migration file to migrate if no migration exists yet. However, this still requires a little bit of manual
     * work as we can't decidedly know if no migration exists yet. However, this runs only when we're also creating a model.
     *
     * @param      $namespace
     * @param      $model
     * @param      $resource
     * @param int  $index
     * @param bool $force
     */
    private function createMigrationIfNotExisting(string $namespace, string $model, array $resource, $index = 0, $force = false)
    {
        $modelBasePath = trim(config('json-schema.models.path'), '/') . '/';
        $modelPath = base_path($modelBasePath . $this->dir($namespace) . $model) . '.php';

        $index = str_pad($index, 2, "0", STR_PAD_LEFT);
        $idx = '00' . $index . '00';

        $migrationFileName = date('Y_m_d') . '_' . $idx . '_' . strtolower($namespace) . '_create_' . Str::snake($model) . '_table';
        $migrationPath = base_path('database/migrations/' . $migrationFileName) . '.php';

        if (!File::exists($modelPath) || $force) {
            $this->info(str_pad("Creating Migration: ", self::$PAD_LENGTH) . $migrationPath);
            $migrationContent = $this->migrationGenerator->create($resource, $namespace, $model);
            file_put_contents($migrationPath, $migrationContent);
            $this->files_generated[] = $migrationPath;
        }
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
     * Add a route to the output which needs to be copied over.
     *
     * @param string $route
     */
    public function addFrontendRoute(string $route)
    {
        $this->frontend_routes[] = $route;
    }

    /**
     * Add a import to the output which needs to be copied over.
     *
     * @param string $import
     */
    public function addFrontendImport(string $import)
    {
        $this->frontend_imports[] = $import;
    }

    /**
     * Add a route to the output which needs to be copied over.
     *
     * @param string $type
     * @param string $route
     * @param string $controller
     */
    public function addRoute(string $type, string $route = '', string $controller = '')
    {
        if ($route == '' || $controller == '') {
            $this->routes[] = $type;
        } else {
            $this->routes[] = "" . 'Route::' . $type . '("' . $route . '", "' . $controller . '");';
        }
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
