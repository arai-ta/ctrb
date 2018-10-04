<?php

namespace Ctrb;

use ChatWork\OAuth2\Client\ChatWorkProvider;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Token\AccessToken;

class App
{

    public $accessToken =<<<TOKEN
****
TOKEN;


    public function execute()
    {

        $provider = new ChatWorkProvider(
            '****',
            '****',
            "****"
        );

        $url = $provider->getAuthorizationUrl();

        echo $url;

        if (isset($_GET['code'])) {
            $token = $provider->getAccessToken(new AuthorizationCode(), [
                'code' => $_GET['code']
            ]);

            echo '<hr>';

            echo $token;
        }

        $owner = $provider->getResourceOwner(new AccessToken(['access_token' => $this->accessToken]));

        echo '<hr>';

        var_dump($owner);
    }
}


