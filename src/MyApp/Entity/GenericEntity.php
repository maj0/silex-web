<?php

namespace MyApp\Entity;

class GenericEntity implements \ArrayAccess
{
    private $data = array();
    
    /*public function __invoke() {
        return $this->data;
        //return 'TA';
    }
    
     public function __call($k,$v=false) {
        return isset($this->data[$k]) ? $this->data[$k] : $this->data[$k] = $v;
    }*/
    
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return isset($this->data[$offset])?$this->data[$offset]:null;
    }
    
    public function offsetSet($offset, $value)
    {
        return $this->data[$offset] = $value;
    }
}
