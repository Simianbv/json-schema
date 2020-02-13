<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends ResourceController
{

    public function index(Request $request, $scope, $model)
    {
        $resource = $this->resource($scope, $model);

        if (!$resource) {
            return new JsonResponse(['error' => 'No valid resource found. Unable to return a LayoutInterface.'], 404);
        }

        return new JsonResponse($resource->filters($request)->toSchema());
    }

}
