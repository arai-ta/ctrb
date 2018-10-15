<?php

namespace Ctrb\Chatwork;

use ChatWork\OAuth2\Client\ChatWorkProvider;
use ChatWork\OAuth2\Client\ChatWorkResourceOwner;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;
use Monolog\Logger;

class Auth
{

    /** @var Logger */
    private $logger;

    /** @var ChatWorkProvider */
    private $oauth;

    public function __construct($oauth = null, $logger = null)
    {
        $this->oauth = $oauth ?: new ChatWorkProvider(
            getenv('CHATWORK_OAUTH_CLIENT_ID'),
            getenv('CHATWORK_OAUTH_CLIENT_SECRET'),
            getenv('CHATWORK_OAUTH_CLIENT_REDIRECT_URL')
        );
        $this->logger = $logger ?: $GLOBALS['logger'];
    }

    public function getConsentUrl(): string
    {
        return $this->oauth->getAuthorizationUrl([
            'scope' => [
                'users.profile.me:read',
                'users.tasks.me:read',
            ]
        ]);
    }

    public function authByCode($code): AccessToken
    {
        try {
            return $this->oauth->getAccessToken(new AuthorizationCode(), [
                'code' => $code
            ]);
        } catch (\Exception $e) {
            $this->logger->warn('code auth failed:'.$code);
            $this->logger->warn($e);
            return null;
        }
    }

    public function authRefresh($token): AccessToken
    {
        return $this->oauth->getAccessToken(new RefreshToken(), [
            'refresh_token' => $token,
        ]);
    }

    /**
     * @param AccessToken $token
     * @return ChatWorkResourceOwner
     */
    public function owner(AccessToken $token): ChatWorkResourceOwner
    {
        return $this->oauth->getResourceOwner($token);
    }
}


