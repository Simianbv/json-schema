<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

/**
 * @class   ApiController
 * @package Simianbv\JsonSchema\Http
 */
class ApiController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $acl_verification = false;

    /**
     * @return bool
     */
    public function requiresAclVerification ()
    {
        return $this->acl_verification === true;
    }

    /**
     * @return bool
     */
    public function isExcludedFromAclVerification ()
    {
        return $this->acl_verification === false;
    }

}
