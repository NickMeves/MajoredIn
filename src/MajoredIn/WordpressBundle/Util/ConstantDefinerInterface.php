<?php

namespace MajoredIn\WordpressBundle\Util;

interface ConstantDefinerInterface
{
    /**
     * Define the necessary constants.  Perform the defines only once.
     */
    public function define();
}