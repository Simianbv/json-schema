<?php
/**
 * @author        Merijn
 * @copyright (c) 2019.
 */

namespace Simianbv\JsonSchema\Console\Generators;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @class   FormGenerator
 * @package App\Console\Lightning
 */
class Form extends Base
{
    /**
     * @var string
     */
    protected $stubLocation = '';

    /**
     * @var string
     */
    private $stub;

    public function __construct()
    {
        $this->stubLocation = config('json-schema.stubs.form');
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return bool
     */
    public function create($resource, $namespace, $model)
    {

        $slugNamespace = Str::slug(strtolower($namespace));
        $slugModel = Str::slug(Str::snake(Str::plural($model)));

        $this->stub = file_get_contents($this->stubLocation);
        $url = ($namespace != '' ? $slugNamespace . '/' : '') . $slugModel . '/';

        $uri = ($namespace != '' ? $slugNamespace . '/' : '') . $slugModel . '/';

        $fields = [
            'Model' => $model,
            'Uri' => $uri,
            'Service' => '$' . ($namespace != '' ? $slugNamespace : 'http'),
            'Name' => ucfirst(strtolower(str_replace('_', ' ', Str::snake($model)))),
            'Url' => $url,
        ];


        $content = $this->fillStub($fields, $this->stub, ['{%', '%}']);

        // Feedback to add to the output
        $file = ucfirst(Str::slug(Str::snake($model)) . "-form");
        $fileTrimmed = ucfirst(Str::slug(Str::snake($model)));
        $component = ucfirst(strtolower($namespace) . $model) . "Form";

        $this->parent->addFrontendImport('const ' . $component . ' = () => import(/* webpackChunkName: "' . strtolower($namespace) . '" */ "./views/' . strtolower($namespace) . '/' . $file . '.vue")');
        $this->parent->addFrontendRoute("{path: '/" . ($namespace != '' ? strtolower($namespace) . '/' : '') . Str::slug(Str::snake(Str::plural($model))) . "/create',      name: '" . ($namespace != '' ? strtolower($namespace) . '-' : '') . strtolower($fileTrimmed) . "-create', component: " . $component . ", props: true}, ");
        $this->parent->addFrontendRoute("{path: '/" . ($namespace != '' ? strtolower($namespace) . '/' : '') . Str::slug(Str::snake(Str::plural($model))) . "/:id/edit',   name: '" . ($namespace != '' ? strtolower($namespace) . '-' : '') . strtolower($fileTrimmed) . "-edit', component: " . $component . ", props: true}, ");

        return $content;
    }


}

