<?php

namespace djchen\OAuth2\Client\Test\Provider;

use GuzzleHttp\Client;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use djchen\OAuth2\Client\Provider\Fitbit;
use djchen\OAuth2\Client\Provider\FitbitOptionsProvider;
use PHPUnit_Framework_TestCase as TestCase;

class FitbitTest extends TestCase
{
    /**
     * @var Fitbit
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Fitbit([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayNotHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testScopes()
    {
        $options = ['scope' => [uniqid()]];
        $url = $this->provider->getAuthorizationUrl($options);
        $this->assertContains(urlencode(implode(' ', $options['scope'])), $url);
    }

    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        $this->assertEquals('/oauth2/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/oauth2/token', $uri['path']);
    }

    public function testOptionsProvider()
    {
        $optionsProvider = $this->provider->getOptionProvider();
        $this->assertInstanceOf(FitbitOptionsProvider::class, $optionsProvider);
    }

    /**
     * Test revoke works for regression purposes
     * Doesn't actually make a request, just to test no exceptions are thrown.
     */
    public function testRevoke()
    {
        $httpMock = \Mockery::mock(Client::class);
        $responseMock = \Mockery::mock(ResponseInterface::class);
        $httpMock->shouldReceive('send')->andReturn($responseMock);
        $this->provider = new Fitbit([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ], [
            'httpClient' => $httpMock,
        ]);
        $token = new AccessToken(['access_token' => 'atoken']);
        $result = $this->provider->revoke($token);
    }
}
