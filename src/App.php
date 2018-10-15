<?php

namespace Ctrb;

use ChatWork\OAuth2\Client\ChatWorkProvider;
use ChatWork\OAuth2\Client\ChatWorkResourceOwner;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class App
{

    private $logger;

    public function __construct($logger =  null)
    {
        $this->logger = $logger ?: $GLOBALS['logger'];
    }

    public function execute()
    {

        $provider = new ChatWorkProvider(
            getenv('CHATWORK_OAUTH_CLIENT_ID'),
            getenv('CHATWORK_OAUTH_CLIENT_SECRET'),
            getenv('CHATWORK_OAUTH_CLIENT_REDIRECT_URL')
        );

        $url = $provider->getAuthorizationUrl([
            'scope' => [
                'users.profile.me:read',
                'users.tasks.me:read',
            ]
        ]);

        echo $url;

        if (isset($_GET['code'])) {

            try {
                $token = $provider->getAccessToken(new AuthorizationCode(), [
                    'code' => $_GET['code']
                ]);

                echo '<hr>';

                var_dump($token);
            } catch (IdentityProviderException $e) {
                echo '<hr>';
                var_dump($e->getMessage());
                $this->logger->debug('authenticationTokenの有効期限切れです');
                exit('authenticationTokenの有効期限切れです');
            }
            $access_token = $token;
        } else {
            $access_token = $provider->getAccessToken(new RefreshToken(), ['refresh_token' => getenv('EXAMPLE_USER_REFRESH_TOKEN')]);
        }

        echo '<hr>';

        var_dump($access_token->getToken());
        if ($access_token->getExpires()) {
            var_dump($access_token->getExpires());
            var_dump($access_token->hasExpired());
        } else {
            var_dump('$access_token is not have "expires"');
        }
        var_dump($access_token->getRefreshToken());
        var_dump($access_token->getResourceOwnerId());
        var_dump($access_token->getValues());

        echo '<hr>';
        /** @var ChatWorkResourceOwner $owner */
        $owner = $provider->getResourceOwner($access_token);
        var_dump($owner->getName());

        try {
            $request = (new Request('GET', 'https://api.chatwork.com/v2/my/tasks'))
                ->withHeader('Authorization', sprintf('Bearer %s', $access_token));

            $client = new Client();

            $response = $client->send($request);

            var_dump($response->getStatusCode());
            var_dump($response->getBody()->getContents());
        } catch (ClientException $e) {
            var_dump($e->getMessage());
            exit;
        }

    }
}


