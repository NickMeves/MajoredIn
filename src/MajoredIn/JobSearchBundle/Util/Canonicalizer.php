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
    
    public function dash($string)
    {
        $string = preg_replace('/-/', '_', $string); //allows - in queries
    
        $string = preg_replace('/\s+/', ' ', $string);
        $string = preg_replace('/^\s/', '', $string);
        $string = preg_replace('/\s$/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
    
        $string = preg_replace('/\//', '', $string); //fixes / and route issues.
    
        return $string;
    }
    
    public function undash($string)
    {
        $string = preg_replace('/-/', ' ', $string);
        $string = preg_replace('/_/', '-', $string);
        return $string;
    }
    
    public function formatLocation($location) {
        $tokens = explode(" ", $this->canonicalize($location));
        $states = array( "AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC",
                "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA",
                "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE",
                "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC",
                "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
    
        $last = count($tokens)-1;
        if (in_array(strtoupper($tokens[$last]), $states) && $last >= 0) {
            $tokens[$last] = strtoupper($tokens[$last]);
            if ($last > 0) {
                $tokens[$last-1] = $tokens[$last-1] . ',';
            }
            for ($i = 0; $i < $last; ++$i) {
                $tokens[$i] = ucwords($tokens[$i]);
            }
            $location = implode(" ", $tokens);
        }
        else {
            $location = ucwords($location);
        }
    
        return $location;
    }
}
