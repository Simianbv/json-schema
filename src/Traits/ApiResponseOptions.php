<?php

/**
 * @copyright (c) Simian B.V. 2020
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

/**
 * Trait ApiResponseOptions
 *
 * @package Simianbv\JsonSchema\Traits
 */
trait ApiResponseOptions
{

    /**
     * Return a default debug response, this is a utility to help with not killing a request
     * and "force" output back over the request.
     *
     * @param       $message
     * @param array $data
     * @param int   $status
     *
     * @return ResponseFactory|Response
     */
    public function debug($message, array $data = [], $status = 418)
    {
        return $this->defaultResponse($message, $data, $status, 'debug-request');
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function success($message, $data = null, $status = 200)
    {
        return $this->defaultResponse($message, $data, $status);
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function info($message, $data = null, $status = 200)
    {
        return $this->defaultResponse(
            [
                'message' => $message,
                'status' => 'info',
                'title' => 'Informatie',
                'type' => 'info',
            ], $data, $status
        );
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function deleted($message, $data = null, $status = 200)
    {
        return $this->defaultResponse($message, $data, $status);
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function created($message, $data = null, $status = 201)
    {
        return $this->defaultResponse($message, $data, $status);
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed|null   $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function updated($message, $data = null, $status = 200)
    {
        return $this->defaultResponse($message, $data, $status);
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function error($message, $data = null, $status = 404)
    {
        return $this->defaultResponse($message, $data, $status);
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function warning($message, $data = null, $status = 404)
    {
        return $this->defaultResponse(
            [
                'message' => $message,
                'status' => 'warning',
                'title' => 'Waarschuwing',
                'type' => 'warning',
            ], $data, $status
        );
    }

    /**
     * @param mixed|string|array $message
     * @param array|mixed        $data
     * @param int                $status
     *
     * @return ResponseFactory|Response
     */
    public function locked($message, $data = null, $status = 200)
    {
        return $this->defaultResponse(
            [
                'message' => $message,
                'status' => 'warning',
                'error' => 'Resource is locked and therefore immutable',
                'title' => 'Waarschuwing',
                'type' => 'warning',
            ], $data, $status
        );
    }

    /**
     * @param        $message
     * @param        $data
     * @param        $status
     *
     * @param string $statusMessage
     *
     * @return ResponseFactory|Response
     */
    public function defaultResponse($message, $data, $status, string $statusMessage = null)
    {
        $body = [];

        if (is_int($data) && $data >= 100 && $data < 600) {
            $status = $data;
            $data = null;
        }
        if (is_array($message) && isset($message['message'])) {
            $body = $message;
        } else {
            if (is_array($message)) {
                $body['message'] = $message;
            } else {
                if (is_object($message)) {
                    if (method_exists($message, 'toString')) {
                        $body['message'] = $message->toString();
                    } else {
                        $body['message'] = $message;
                    }
                } else {
                    $body['message'] = $message;
                }
            }
        }

        if ($statusMessage !== '') {
            $body['status'] = $statusMessage;
        }

        if (is_string($body['message'])) {
            $body['message'] = __($body['message']);
        }

        if ($data !== null) {
            $body['meta'] = $data;
        }

        if (is_array($data)) {
            if (isset($data['exception']) && $data['exception'] instanceof Exception) {
                $body['meta']['exception_message'] = $data['exception']->getMessage();
                $body['meta']['exception_trace'] = $data['exception']->getTrace();
            }
        }

        return response($body, $status);
    }

}
