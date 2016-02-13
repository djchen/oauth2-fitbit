<?php

namespace djchen\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class FitbitUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $userInfo = [];

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->userInfo = $response['user'];
    }

    public function getId()
    {
        return $this->userInfo['encodedId'];
    }

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->userInfo['displayName'];
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->userInfo;
    }
}
