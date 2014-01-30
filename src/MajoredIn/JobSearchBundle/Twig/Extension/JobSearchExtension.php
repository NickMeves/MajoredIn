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
            'formatLoc' => new \Twig_Filter_Method($this, 'formatLocation'),
            'dash'  => new \Twig_Filter_Method($this, 'dash'),
            'addarticle' => new \Twig_Filter_Method($this, 'addArticle')
        );
    }
    
    public function getName() {
        return 'mi_search.twig.extension';
    }
    
    public function formatLocation($location) {
        return $this->canonicalizer->formatLocation($location);
    }
    
    public function dash($string) {
        return $this->canonicalizer->dash($string);
    }
    
    public function addArticle($string, $first = false) {
        $string = strtolower($string);
        if ($first) {
            return (preg_match('/^[aeiou]/', $string) ? 'An ' : 'A ') . $string;
        }
        else {
            return (preg_match('/^[aeiou]/', $string) ? 'an ' : 'a ') . $string;
        }
    }
}