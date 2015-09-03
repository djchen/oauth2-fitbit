<?php

namespace djchen\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Fitbit extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Fitbit URL.
     *
     * @const string
     */
    const BASE_FITBIT_URL = 'https://www.fitbit.com';

    /**
     * Fitbit API URL.
     *
     * @const string
     */
    const BASE_FITBIT_API_URL = 'https://api.fitbit.com';

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return static::BASE_FITBIT_URL . '/oauth2/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return static::BASE_FITBIT_API_URL . '/oauth2/token';
    }

    /**
     * Returns the url to retrieve the resource owners's profile/details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return static::BASE_FITBIT_API_URL . '/1/user/-/profile.json';
    }

    /**
     * Returns the default scopes used by Fitbit.
     *
     * Fitbit OAuth 2.0 Beta Note:
     * Currently, activity, nutrition, profile, settings, sleep, social, and weight are
     * required scopes and are equivalent to OAuth 1.0a access.
     *
     * If you do not specify these scopes, they will be appended to the list
     * your application specifies. Fitbit is still updating all existing endpoints to
     * support the relevant scopes. A scope will be removed from the required list when
     * all related endpoints are updated. All scopes will be optional in the future.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['activity', 'settings', 'nutrition', 'social', 'profile', 'sleep', 'weight'];
    }

    /**
     * Checks Fitbit API response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['errors'][0])) {
            $message = $data['errors'][0]['errorType'] . ': ' . $data['errors'][0]['message'];
            throw new IdentityProviderException($message, $response->getStatusCode(), $data);
        }
    }

    /**
     * Returns the string used to separate scopes.
     *
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Returns authorization parameters based on provided options.
     * Fitbit does not use the 'approval_prompt' param and here we remove it.
     *
     * @param array $options
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $params = parent::getAuthorizationParameters($options);
        unset($params['approval_prompt']);
        if (!empty($options['prompt'])) {
            $params['prompt'] = $options['prompt'];
        }
        return $params;
    }

    /**
     * Builds request options used for requesting an access token.
     *
     * @param  array $params
     * @return array
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = parent::getAccessTokenOptions($params);
        $options['headers']['Authorization'] =
            'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
        return $options;
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return FitbitUser
     */
    public function createResourceOwner(array $response, AccessToken $token)
    {
        return new FitbitUser($response);
    }
}
