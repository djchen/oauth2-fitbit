<?php

namespace djchen\OAuth2\Client\Test\Provider;

use djchen\OAuth2\Client\Provider\Fitbit;
use Eloquent\Phony\Phpunit\Phony;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class FitbitTest extends TestCase
{
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

    public function testGetFitbitRateLimit()
    {
        $response = Phony::mock(ResponseInterface::class);
        $response->getStatusCode->returns(429);
        $response->getHeader->with('Retry-After')->returns(['1234']);
        $response->getHeader->with('Fitbit-Rate-Limit-Limit')->returns(['150']);
        $response->getHeader->with('Fitbit-Rate-Limit-Remaining')->returns(['100']);
        $response->getHeader->with('Fitbit-Rate-Limit-Reset')->returns(['2345']);
        $rateLimit = $this->provider->getFitbitRateLimit($response->get());
        $this->assertEquals('1234', $rateLimit->getRetryAfter());
        $this->assertEquals('150', $rateLimit->getLimit());
        $this->assertEquals('100', $rateLimit->getRemaining());
        $this->assertEquals('2345', $rateLimit->getReset());
    }

        public function testGetFitbitRateLimitMissingHeaders()
    {
        $response = Phony::mock(ResponseInterface::class);
        $response->getStatusCode->returns(200);
        $response->getHeader->with('Fitbit-Rate-Limit-Limit')->returns(null);
        $response->getHeader->with('Fitbit-Rate-Limit-Remaining')->returns(null);
        $response->getHeader->with('Fitbit-Rate-Limit-Reset')->returns(null);
        $rateLimit = $this->provider->getFitbitRateLimit($response->get());
        $this->assertNull($rateLimit->getRetryAfter());
        $this->assertNull($rateLimit->getLimit());
        $this->assertNull($rateLimit->getRemaining());
        $this->assertNull($rateLimit->getReset());
    }
}
