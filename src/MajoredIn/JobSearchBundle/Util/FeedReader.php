<?php

namespace MajoredIn\JobSearchBundle\Util;

class FeedReader implements FeedReaderInterface
{
    protected $handle;
    
    public function __construct(array $options = null)
    {
        $this->handle = curl_init();
        
        foreach ($options as $option => $value) {
            curl_setopt($this->handle, constant($option), $value);
        }
    }
    
    public function __destruct()
    {
        if (isset($this->handle)) {
            curl_close($this->handle);
        }
    }
    
    public function readUrl($url)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        $result = curl_exec($this->handle);
        
        return $result;
    }
}
