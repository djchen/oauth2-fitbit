<?php

namespace djchen\OAuth2\Client\Provider;

use League\OAuth2\Client\OptionProvider\PostAuthOptionProvider;

class FitbitOptionsProvider extends PostAuthOptionProvider
{
    /**
     * The fitbit client id
     * @var string
     */
    private $clientId;

    /**
     * the fitbit client secret
     * @var string
     */
    private $clientSecret;

    /**
     * Set the client id and secret
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Builds request options used for requesting an access token.
     *
     * @param string $method
     * @param  array $params
     * @return array
     */
    public function getAccessTokenOptions($method, array $params)
    {
        $options = parent::getAccessTokenOptions($method, $params);
        $options['headers']['Authorization'] =
            'Basic '.base64_encode($this->clientId.':'.$this->clientSecret);

        return $options;
    }
}
