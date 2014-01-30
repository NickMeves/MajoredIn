<?php

namespace MajoredIn\JobSearchBundle\Util;

interface FeedReaderInterface
{
    /**
     * Send a request to a URL and get the response
     *
     * @return Reponse in format specified
     * @throws Exception, GatewayTimeoutException
     */
    public function get($url);
}
