<?php
namespace App\DataObjects;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ResponseData
 * @package Ewave\DataObjects
 */
class ResponseDTO
{
    /**
     * Creates an instance of the class.
     *
     * @param bool $success   : Indicates whether the request is successful.
     * @param string $message : The customer's message.
     * @param int $errorCode  : The error code of the ResponseData.
     * @param int $status
     * @param array $params   : Additional parameters for the ResponseData.
     */
    public function __construct(
        /**
         * @Serializer\Type("bool")
         * @Serializer\SerializedName("success")
         */
        protected bool $success = true,
    
        /**
         * @Serializer\Type("string")
         * @Serializer\SerializedName("errorMessage")
         */
        protected string $message = '',
    
        /**
         * @Serializer\Type("int")
         * @Serializer\SerializedName("errorCode")
         */
        protected int $errorCode = 0,
    
        /**
         * @Serializer\Type("int")
         * @Serializer\SerializedName("status")
         */
        protected int $status = 200,
    
        /**
         * @Serializer\Type("array")
         * @Serializer\SerializedName("params")
         */
        protected array $params = [])
    {
    }
    
    /**
     * Returns a json response object with the parameters.
     *
     * @return JsonResponse
     */
    public function response(): JsonResponse
    {
        return new JsonResponse($this->toArray(), $this->getStatus());
    }
    
    /**
     * Returns the data as array.
     */
    public function toArray()
    {
        // Getting the get properties excluding getParam.
        $methods = preg_grep('/^((?!getParam)get|getParams)/', get_class_methods($this));
        $jsonMethods = [];
        
        foreach ($methods as $position => $name)
        {
            $jsonMethods[lcfirst(substr($name, 3))] = $this->{$name}();
        }
        
        return $jsonMethods;
    }
    
    /**
     * Sets the indication whether the request is successful.
     *
     * @param bool $isSuccess: True if the request is successful.
     *
     * @return ResponseDTO
     */
    public function setSuccess(bool $isSuccess) : self
    {
        $this->success = $isSuccess;
        
        return $this;
    }
    
    /**
     * Gets the indication whether the request is successful.
     *
     * @return bool: True if the request is successful, otherwise false.
     */
    public function getSuccess() : bool
    {
        return $this->success;
    }
    
    /**
     * Sets the error code.
     *
     * @param int $errorCode: The error code to set.
     *
     * @return ResponseDTO
     */
    public function setErrorCode(int $errorCode) : self
    {
        $this->errorCode = $errorCode;
        return $this;
    }
    
    /**
     * Gets the error code.
     *
     * @return int : The error's code.
     */
    public function getErrorCode() : int
    {
        return $this->errorCode;
    }
    
    /**
     * Sets the error's message.
     *
     * @param string $errorMessage: The error message to set.
     *
     * @return ResponseDTO
     */
    public function setMessage(string $errorMessage) : self
    {
        $this->message = $errorMessage;
        
        return $this;
    }
    
    /**
     * Gets the error's message.
     *
     * @return string : The error's message.
     */
    public function getErrorMessage() : string
    {
        return $this->message;
    }
    
    /**
     * Sets the indication to redirect to 404 page.
     *
     * @param bool $redirect: Indicates whether to redirect to 404 page.
     *
     * @return ResponseDTO
     */
    public function setRedirect404(bool $redirect) : self
    {
        $this->redirect404 = $redirect;
        
        return $this;
    }
    
    /**
     * Gets the indication whether to redirect to 404 page.
     *
     * @return bool : True if to redirect, otherwise false.
     */
    public function getRedirect404() : bool
    {
        return $this->redirect404;
    }
    
    /**
     * Sets the indication whether to logout the user.
     *
     * @param bool $logout: True if to logout the user.
     *
     * @return ResponseDTO
     */
    public function setLogout(bool $logout) : self
    {
        $this->logout = $logout;
        
        return $this;
    }
    
    /**
     * Gets the indication whether to logout the user.
     *
     * @return bool: True if to logout the user.
     */
    public function getLogout() : bool
    {
        return $this->logout;
    }
    
    /**
     * Sets additional params of the ResponseData.
     *
     * @param array $params: The params to set.
     *
     * @return ResponseDTO
     */
    public function setParams(array $params) : self
    {
        $this->params = $params;
        
        return $this;
    }
    
    /**
     * Gets the params of the ResponseData.
     *
     * @return array : The params of the ResponseData.
     */
    public function getParams() : array
    {
        return $this->params;
    }
    
    /**
     * Adds a key to the parameters.
     *
     * @param $key: The name of the key.
     * @param $value: The value to assign to the key.
     *
     * @return ResponseDTO
     */
    public function addParam($key, $value) : self
    {
        if ($this->params == null) {
            $this->params = [];
        }
    
        $this->params[$key] = $value;
        
        return $this;
    }
    
    /**
     * Adds the given parameters.
     *
     * @param array $params: The parameters to add.
     *
     * @return ResponseDTO
     */
    public function addParams(array $params) : self
    {
        foreach ($params as $key => $value)
        {
            $this->addParam($key, $value);
        }
        
        return $this;
    }
    
    /**
     * Gets the given key's value.
     *
     * @param $key: The key to retrieve the value for it.
     
     * @return object|string|null
     */
    public function getParam($key)
    {
        return $this->params[$key] ?? null;
    }
    
    /**
     * @param bool $refresh
     *
     * @return $this
     */
    public function setRefresh(bool $refresh): self
    {
        $this->refresh = $refresh;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getRefresh(): bool
    {
        return $this->refresh;
    }
    
    /**
     * @var bool: Indicates whether to logout the user.
     */
    private bool $logout = false;

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    
    /**
     * @var int: Indicates whether to redirect to 404 error page.
     *
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("redirect404")
     */
    private bool $redirect404 = false;
    
    /**
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("refresh")
     */
    private bool $refresh = false;
}