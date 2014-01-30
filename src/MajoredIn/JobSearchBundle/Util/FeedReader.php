<?php

namespace MajoredIn\JobSearchBundle\Util;

use Guzzle\Http\Client;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\CurlException;
use MajoredIn\JobSearchBundle\Exception\GatewayTimeoutException;
use MajoredIn\JobSearchBundle\Exception\PageNotFoundException;

class FeedReader implements FeedReaderInterface
{
    protected $options;
    
    public function __construct($options = array())
    {
        $this->options = $options;
    }
    
    public function get($url)
    {
        $client = new Client();
        
        try {
            $request = $client->get($url, array(), $this->options);
            $response = $request->send();
        }
        //400s
        catch (ClientErrorResponseException $e) {
            throw new PageNotFoundException();
        }
        //500s
        catch (ServerErrorResponseException $e) {
            throw new GatewayTimeoutException();
        }
        //Connection problems and cURL errors
        catch (CurlException $e) {
            throw new GatewayTimeoutException();
        }

        return $response->getBody();
    }
}
