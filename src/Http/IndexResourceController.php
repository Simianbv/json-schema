<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class   IndexResourceController
 * @package App\Http\Controllers
 */
class IndexResourceController extends ResourceController
{

    /**
     * example:
     *  /auth/users
     *
     * where /auth maps to the namespace and /users maps the model in that namespace
     *
     * @param Request $request
     *
     * @param         $scope
     * @param         $resource
     *
     * @return JsonResponse
     */
    public function handle(Request $request, $scope, $resource)
    {
        $this->resource($scope, $resource);
        $model = $this->getModelClass();

        if ($request->get('id')) {
            $ids = explode(',', $request->get('id'));
            $results = $model::whereIn('id', $ids)->get();
        } else {
            $results = $model::all();
        }

        return new JsonResponse($results);
    }

}
