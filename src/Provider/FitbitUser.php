<?php

namespace djchen\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class FitbitUser implements ResourceOwnerInterface
{
    /**
     * @var string
     */
    protected $encodedId;

    /**
     * @var string
     */
     protected $displayName;

    /**
     * @param  array $response
     */
    public function __construct(array $response)
    {
        $userInfo = $response['user'];
        $this->encodedId = $userInfo['encodedId'];
        $this->displayName = $userInfo['displayName'];
    }

    public function getId()
    {
        return $this->encodedId;
    }

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'encodedId'        => $this->encodedId,
            'displayName'      => $this->displayName
        ];
    }
}
