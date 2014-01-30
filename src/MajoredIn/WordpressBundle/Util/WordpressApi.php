<?php

namespace MajoredIn\WordpressBundle\Util;

class WordpressApi
{
    protected $installPath;
    protected $stateManager;
    protected $constantDefiner;
    protected $wpState;
    
    protected static $loaded = false;
    
    public function __construct($installPath, StateManagerInterface $stateManager, ConstantDefinerInterface $constantDefiner)
    {
        $this->installPath= $installPath;
        $this->stateManager = $stateManager;
        $this->constantDefiner = $constantDefiner;
        $this->wpState = array();
    }
    
    /**
     * Call a native wordpress function within the wordpress global state.
     * 
     * @return mixed the output from the native function
     */
    public function __call($function, $arguments)
    {
        $this->load();
        
        $this->stateManager->captureState();
        
        $this->stateManager->globalize($this->wpState);
        
        $output = call_user_func_array($function, $arguments);
        
        $this->wpState = array_diff_key(array_merge($GLOBALS, get_defined_vars()), array_flip(StateManager::$superglobals));
        $this->wpState = array_diff_key($this->wpState, array('output' => null));
        
        $this->stateManager->restoreState();
        
        return $output;
    }
    
    /**
     * Call a callback function within the wordpress global state.
     * 
     * @param function the function to run in the wordpress state
     * @param [s] parameters for the first callback parameter
     * @return mixed the output from $callback
     */
    public function inScope()
    {
        if (func_num_args() === 0) {
            return;
        }
        
        $this->load();
        
        $this->stateManager->captureState();
        
        $this->stateManager->globalize($this->wpState);
        
        $callback = func_get_arg(0);
        $arguments = array();
        for ($i = 1; $i < func_num_args(); ++$i) {
            $arguments[] = func_get_arg($i);
        }

        if (!is_callable($callback)) {
            throw new \LogicException('The Wordpress scope callback must be a valid PHP callable.');
        }
        $output = call_user_func_array($callback, $arguments);
        
        $this->wpState = array_diff_key(array_merge($GLOBALS, get_defined_vars()), array_flip(StateManager::$superglobals));
        $this->wpState = array_diff_key($this->wpState, array('output' => null));
        
        $this->stateManager->restoreState();
        
        return $output;
    }
    
    /**
     * Call a callback function within the wordpress global state that contains one parameter passed by reference.
     *
     * @param function the function to run in the wordpress state
     * @param parameter passed by reference
     * @return mixed the output from $callback
     */
    public function inScopePassByRef($callback, &$referenceParameter)
    {
        $this->load();
    
        $this->stateManager->captureState();
    
        $this->stateManager->globalize($this->wpState);
    
        if (!is_callable($callback)) {
            throw new \LogicException('The Wordpress scope callback must be a valid PHP callable.');
        }
        $output = $callback($referenceParameter);
    
        $this->wpState = array_diff_key(array_merge($GLOBALS, get_defined_vars()), array_flip(StateManager::$superglobals));
        $this->wpState = array_diff_key($this->wpState, array('output' => null));
    
        $this->stateManager->restoreState();
    
        return $output;
    }
    
    /**
     * Explicitly enable the wordpress scope (for situtations where callbacks won't work)
     */
    public function scopeOn()
    {
        $this->load();
    
        $this->stateManager->captureState();
    
        $this->stateManager->globalize($this->wpState);
    }
    
    /**
     * Explicitly disable the wordpress scope (for situtations where callbacks won't work)
     */
    public function scopeOff()
    {
        $this->wpState = array_diff_key(array_merge($GLOBALS, get_defined_vars()), array_flip(StateManager::$superglobals));
    
        $this->stateManager->restoreState();
    }
    
    /**
     * Load the wordpress API and capture its global state.  Current state is cleaned of wordpress state after running.
     */
    protected function load()
    {
        if ($this->isLoaded()) {
            return;
        }
        
        $this->stateManager->captureState();
        
        $this->constantDefiner->define();
        require_once($this->installPath . '/wp-load.php');
        
        $this->wpState = array_diff_key(array_merge($GLOBALS, get_defined_vars()), array_flip(StateManager::$superglobals));
        static::$loaded = true;
        
        $this->stateManager->restoreState();
    }
    
    /**
     * Test if wordpress wp-load.php has already been loaded
     * 
     * @return boolean True is loaded detected, false otherwise
     */
    protected function isLoaded()
    {
        if (static::$loaded) {
            return true;
        }
        
        // ABSPATH and WPINC must be already defined if Wordpress is loaded
        if(!defined('ABSPATH') || !defined('WPINC')) {
            return false;
        }
        
        return in_array('wp-load.php', preg_replace("/\/.*\//", "", get_included_files()));
    }
}