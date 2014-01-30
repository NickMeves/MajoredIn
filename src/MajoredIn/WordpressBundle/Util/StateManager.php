<?php

namespace MajoredIn\WordpressBundle\Util;

class StateManager implements StateManagerInterface
{
    protected $stateStack;
    
    public static $superglobals = array('GLOBALS','_SERVER','_GET','_POST','_FILES','_COOKIE','_SESSION','_REQUEST','_ENV');
    
    public function __construct()
    {
        $this->stateStack = array();
    }
    
    public function captureState()
    {
        $this->stateStack[] = array_diff_key($GLOBALS, array_flip(static::$superglobals));
    }
    
    public function globalize($variables = array())
    {
        foreach ($variables as $key => $value) {
            if (!in_array($key, static::$superglobals)) {
                $GLOBALS[$key] = $value;
            }
        }
    }
    
    public function restoreState()
    {
        if (empty($this->stateStack)) {
            return;
        }
        
        foreach ($GLOBALS as $key => $value) {
            if (!in_array($key, static::$superglobals)) {
                unset($GLOBALS[$key]);
            }
        }
        
        $restoreState = array_pop($this->stateStack);
        foreach ($restoreState as $key => $value) {
            if (!in_array($key, array_merge(static::$superglobals, array('this')))) {
                $GLOBALS[$key] = $value;
            }
        }
    }
}