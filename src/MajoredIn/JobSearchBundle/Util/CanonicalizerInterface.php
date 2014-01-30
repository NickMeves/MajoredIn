<?php

namespace MajoredIn\JobSearchBundle\Util;

interface CanonicalizerInterface
{
    public function canonicalize($string);
    
    public function dash($string);
    
    public function undash($string);
    
    public function formatLocation($location);
}
