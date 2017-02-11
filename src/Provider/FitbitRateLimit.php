<?php

namespace djchen\OAuth2\Client\Provider;

class FitbitRateLimit
{
    private $retryAfter;
    private $limit;
    private $remaining;
    private $reset;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 429) {
            $retryAfter = $response->getHeader('Retry-After');
        }
        $limit = $response->getHeader('Fitbit-Rate-Limit-Limit');
        $remaining = $response->getHeader('Fitbit-Rate-Limit-Remaining');
        $reset = $response->getHeader('Fitbit-Rate-Limit-Reset');
    }

    /**
     * In the event the request is over the rate limit, Fitbit returns the number
     * of seconds until the rate limit is reset and the request should be retried.
     *
     * @return String Number of seconds until request should be retried.
     */
    public function getRetryAfter()
    {
        return $retryAfter;
    }

    /**
     * @return String The quota number of calls.
     */
    public function getLimit()
    {
        return $limit;
    }

    /**
     * @return String The number of calls remaining before hitting the rate limit.
     */
    public function getRemaining()
    {
        return $remaining;
    }

    /**
     * @return String The number of seconds until the rate limit resets.
     */
    public function getReset()
    {
        return $reset;
    }
}
