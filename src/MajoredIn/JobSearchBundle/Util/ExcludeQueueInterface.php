<?php

namespace MajoredIn\JobSearchBundle\Util;

use Symfony\Component\HttpFoundation\Request;

interface ExcludeQueueInterface
{   
    public function add(Request $request);
    
    public function remove();
    
    public function size();
    
    public function isEmpty();
}
