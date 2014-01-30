<?php

namespace MajoredIn\JobSearchBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class ExcludeQueue implements ExcludeQueueInterface
{
    protected $requests;
    protected $size;
    
    public function __construct() {
        $this->requests = array();
        $this->size = 0;
    }
    
    public function add(Request $request) {
        $this->requests[] = $request;
        $this->size++;
        
        return $this;
    }
    
    public function remove() {
        if (null !== $request = array_shift($this->requests)) {
            $this->size--;
        }
        else {
            $this->size = 0;
        }
        return $request;
    }
    
    public function size() {
        return $this->size;
    }
    
    public function isEmpty() {
        if ($this->size > 0) {
            return false;
        }
        return true;
    }
}
