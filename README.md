# Fitbit Provider for OAuth 2.0 Client

This package provides Fitbit OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

This package is compliant with [PSR-1][], [PSR-2][] and [PSR-4][]. If you notice compliance oversights, please send
a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md


## Requirements

The following versions of PHP are supported.

* PHP 5.4
* PHP 5.5
* PHP 5.6
* HHVM

## Installation

To install, use composer:

```
composer require djchen/oauth2-fitbit
```

## Usage

### Authorization Code Flow

```php

$provider = new djchen\OAuth2\Client\Provider\Fitbit([
    'clientId'          => '{fitbit-oauth2-client-id}',
    'clientSecret'      => '{fitbit-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url'
]);

// start the session
session_start();

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $userDetails = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $userDetails->getDisplayName());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();

    // Use this to get a new access token if the old one expires
    echo $token->getRefreshToken();

    // Unix timestamp of when the token will expire, and need refreshing
    echo $token->getExpires();
}

```

### Refreshing a Token

```php
$provider = new djchen\OAuth2\Client\Provider\Fitbit([
    'clientId'          => '{fitbit-oauth2-client-id}',
    'clientSecret'      => '{fitbit-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url'
]);

$grant = new League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/djchen/oauth2-fitbit/blob/master/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](https://github.com/djchen/oauth2-fitbit/blob/master/LICENSE) for more information.
