<?php

namespace MajoredIn\JobSearchBundle\Util;

class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize($string)
    {
        $canon = $string;
        
        $canon = preg_replace('/[\+\-]/', ' ', $canon);
        $canon = preg_replace('/[,\'\.]/', '', $canon); //remove commonly omitted keys so with or without them matches
        $canon = mb_convert_case($canon, MB_CASE_LOWER, mb_detect_encoding($canon));
        
        $canon = preg_replace('/\s+/', ' ', $canon);
        $canon = preg_replace('/^\s/', '', $canon);
        //$canon = preg_replace('/\s$/', '', $canon); //allow 1 trailing space for autocomplete
        
        return $canon;
    }
}
