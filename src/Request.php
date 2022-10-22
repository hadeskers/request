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
    public static $FORM_TYPE_JSON = 'application/json';

    protected $url;
    protected $method;
    protected $params;
    protected $bodies;
    protected $formType;
    protected $headers;

    protected $responseHeaders = [];
    protected $cookies = [];
    protected $responseContent;
    protected $responseStatusCode;
    protected $_http_response_header;

    public function __construct($url = null, $method = 'GET')
    {
        $this->url = $url;
        $this->method = $method;
        $this->formType = self::$FORM_TYPE_ENCODED;
    }

    //region Private methods
    private function buildRequest()
    {
        $headers = is_array($this->headers) && count($this->headers) ? $this->headers : [];
        if(isset($headers['cookie'])){
            $this->setCookieString($headers['cookie']);
        }
        if($this->method != self::$METHOD_GET){
            $headers['Content-Type'] = $this->formType;
        }
        $this->formType = $headers['Content-Type'] ?? self::$FORM_TYPE_ENCODED;
        $headers['cookie'] = $this->getCookieString();
        $headerString = implode("\r\n", array_map(function ($item, $key){
            return "{$key}: {$item}";
        }, $headers, array_keys($headers)));

        if($this->formType == self::$FORM_TYPE_JSON){
            $content = json_encode($this->bodies);
        } else {
            $content = http_build_query(is_array($this->bodies) && count($this->bodies) ? $this->bodies : []);
        }

        $options = [
            'http' => [
                'method' => $this->method,
                'header' => $headerString,
                'content' => $content,
                'ignore_errors' => true,
            ]
        ];
        $context = stream_context_create($options);
        $this->responseContent = file_get_contents($this->buildUrl(), false, $context);
        $this->_http_response_header = $http_response_header;
        $this->setResponseHeaders();
    }

    private function setResponseHeaders(){
        $this->responseHeaders = [];
        foreach ($this->_http_response_header as $item){
            if(stripos($item, ':') !== false){
                $split = explode(':', $item);
                if($split[0] == 'Set-Cookie'){
                    $cookieString = explode(';', trim($split[1]))[0];
                    $equalIndex = stripos($cookieString, '=');
                    $name = substr($cookieString, 0, $equalIndex);
                    $value = substr($cookieString, $equalIndex+1);
                    $this->cookies[$name] = $value;
                } else {
                    $this->responseHeaders[$split[0]] = trim($split[1]);
                }
            } else if(stripos($item, 'HTTP/1.0') !== false){
                $split = explode(' ', $item);
                $this->responseStatusCode = intval($split[1]);
            }
        }
    }

    private function buildUrl()
    {
        return $this->url . '?' . http_build_query(is_array($this->params) && count($this->params) ? $this->params : []);
    }
    //endregion

    //region Setter methods
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setParams(array $params)
    {
        foreach ($params as $name => $value){
            $this->params[$name] = $value;
        }
        return $this;
    }

    public function setBodies(array $bodies)
    {
        foreach ($bodies as $name => $value){
            $this->bodies[$name] = $value;
        }
        return $this;
    }

    public function setFormType($formType)
    {
        $this->formType = $formType;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value){
            $this->headers[$name] = $value;
        }
        return $this;
    }

    public function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    public function setCookies(array $cookies)
    {
        foreach ($cookies as $name=>$value){
            $this->cookies[$name] = $value;
        }
        return $this;
    }

    public function setCookieString(string $cookieString)
    {
        $split = explode(';', $cookieString);
        foreach ($split as $item){
            $equalIndex = stripos($item, '=');
            $name = substr($item, 0, $equalIndex);
            $value = trim(substr($item, $equalIndex+1));
            $this->cookies[$name] = $value;
        }
        return $this;
    }
    //endregion

    //region Action methods
    public function get($url = null, $params = null)
    {
        $this->url = $url ? $url : $this->url;
        $this->setParams($params ?? []);
        $this->method = self::$METHOD_GET;
        $this->buildRequest();
        return $this;
    }

    public function post($url = null, $bodies = null)
    {
        $this->url = $url ? $url : $this->url;
        $this->setBodies($bodies ?? []);
        $this->method = self::$METHOD_POST;
        $this->buildRequest();
        return $this;
    }

    public function put($url = null, $bodies = null)
    {
        $this->url = $url ? $url : $this->url;
        $this->setBodies($bodies ?? []);
        $this->method = self::$METHOD_PUT;
        $this->buildRequest();
        return $this;
    }

    public function delete($url = null, $params = null)
    {
        $this->url = $url ? $url : $this->url;
        $this->setParams($params ?? []);
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

    public function getHeaders()
    {
        return $this->responseHeaders;
    }

    public function getStatusCode()
    {
        return $this->responseStatusCode;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getCookieString()
    {
        return implode("; ", array_map(function ($item, $key){
            return "{$key}={$item}";
        }, $this->cookies, array_keys($this->cookies)));
    }
    //endregion
}
