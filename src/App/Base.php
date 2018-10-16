<?php

namespace Ctrb\App;

use Monolog\Logger;

class Base
{

    /** @var Logger */
    protected $logger;

    public function __construct($logger =  null)
    {
        $this->logger = $logger ?: $GLOBALS['logger'];
    }

    public function redirect($path) {
        header('Location: '.$path);
    }

    public function execute()
    {

        $user = new User();

        if ($user->isLoggedIn()) {

        }

        $auth = new Chatwork\Auth();

        $url = $auth->getConsentUrl();

        echo $url;

        if (isset($_GET['code'])) {
            $access_token = $auth->authByCode($_GET['code']);
        } else {
            $access_token = $auth->authRefresh(getenv('EXAMPLE_USER_REFRESH_TOKEN'));
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
        $owner = $auth->owner($access_token);
        $account_id = $owner->getAccountId();

        $_SESSION['account_id'] = $account_id;

        $user = new Chatwork\Api($access_token);
        var_dump($user->getMyTasks());

    }
}


