<?php

namespace MajoredIn\JobSearchBundle\Twig\Extension;

use MajoredIn\JobSearchBundle\Util\CanonicalizerInterface;

class JobSearchExtension extends \Twig_Extension
{
    protected $canonicalizer;
    
    public function __construct(CanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }
    
    public function getFilters()
    {
        return array(
            'diff'	=> new \Twig_Filter_Function('array_diff_assoc'),
            'ucwords'	=> new \Twig_Filter_Function('ucwords'),
            'strtolower'	=> new \Twig_Filter_Function('strtolower'),
            'dash'  => new \Twig_Filter_Function('MajoredIn\JobSearchBundle\Controller\JobSearchController::dash'),
            'formatLoc' => new \Twig_Filter_Method($this, 'formatLoc'),
        );
    }
    
    public function getName() {
        return 'mi_search.twig.extension';
    }
    
    
    //TODO: UNIT TEST THIS!
    public function formatLoc($location) {
        $tokens = explode(" ", $this->canonicalizer->canonicalize($location));
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