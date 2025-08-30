<?php
namespace FP4P\Component\JSports\Site\Objects\Validators;

class ValidatorResponse
{
    
    protected $code;
    protected $msg;
    
    function __construct($code, $msg = "") {
        $this->code = $code;
        $this->msg = $msg;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param mixed $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    } 
    
    public function isValid() {
        if ($this->code > 0) {
            return false;
        } else {
            return true;
        }
    }
}

