<?php
/**
 * Created by PhpStorm.
 * User: hadesker
 * Date: 05/08/19
 * Time: 10:43 PM
 */

namespace Hadesker\Request;

class Request
{
    public static $METHOD_GET = 'GET';
    public static $METHOD_POST = 'POST';
    public static $METHOD_PUT = 'PUT';
    public static $METHOD_DELETE = 'DELETE';

    public static $FORM_TYPE_ENCODED = 'application/x-www-form-urlencoded';
    public static $FORM_TYPE_DATA = 'multipart/form-data';

    protected $url;
    protected $method;
    protected $params;
    protected $bodies;
    protected $formType;
    protected $headers;

    protected $responseContent;

    public function __construct($url = null, $method = 'GET')
    {
        $this->url = $url;
        $this->method = $method;
        $this->formType = self::$FORM_TYPE_ENCODED;
    }

    //region Private methods
    private function buildRequest()
    {
        $headers = $this->headers ?? [];
        $headers['Content-type'] = $this->formType;
        $headerString = implode("\r\n", array_map(function ($item, $key){
            return "{$key}: {$item}";
        }, $headers, array_keys($headers)));

        $options = [
            'http' => [
                'method' => $this->method,
                'header' => $headerString,
                'content' => http_build_query($this->bodies ?? []),
            ]
        ];
        $context = stream_context_create($options);
        $this->responseContent = file_get_contents($this->buildUrl(), false, $context);
    }

    private function buildUrl()
    {
        return $this->url . '?' . http_build_query($this->params ?? []);
    }
    //endregion

    //region Setter methods
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public function setBodies(array $bodies)
    {
        $this->bodies = $bodies;
        return $this;
    }

    public function setFormType(string $formType)
    {
        $this->formType = $formType;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
    //endregion

    //region Action methods
    public function get(string $url = null, array $params = null)
    {
        $this->url = $url ?? $this->url;
        $this->params = $params ?? $this->params;
        $this->responseContent = file_get_contents($this->buildUrl());
        return $this;
    }

    public function post(string $url = null, array $bodies = null)
    {
        $this->url = $url ?? $this->url;
        $this->bodies = $bodies ?? $this->bodies;
        $this->method = self::$METHOD_POST;
        $this->buildRequest();
        return $this;
    }

    public function put(string $url = null, array $bodies = null)
    {
        $this->url = $url ?? $this->url;
        $this->bodies = $bodies ?? $this->bodies;
        $this->method = self::$METHOD_PUT;
        $this->buildRequest();
        return $this;
    }

    public function delete(string $url = null, array $params = null)
    {
        $this->url = $url ?? $this->url;
        $this->params = $params ?? $this->params;
        $this->method = self::$METHOD_DELETE;
        $this->buildRequest();
        return $this;
    }
    //endregion

    //region Utilities methods
    public function getResponse()
    {
        return $this->responseContent;
    }

    public function getResponseObject()
    {
        return json_decode($this->getResponse());
    }

    public function getResponseArray()
    {
        return json_decode($this->getResponse(), true);
    }
    //endregion
}
