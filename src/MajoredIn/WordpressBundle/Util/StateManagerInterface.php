<?php

namespace MajoredIn\WordpressBundle\Util;

interface StateManagerInterface
{
    /**
     * Captures the existing state of the GLOBAL supervariable
     */
    public function captureState();
    
    /**
     * Adds the array (key => var_name, value => var_value) to the global scope.
     */
    public function globalize($variables = array());
    
    /**
     * Removes any extra variables that were globalize and restored the original state.
     */
    public function restoreState();
}