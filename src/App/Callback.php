<?php

namespace Ctrb\App;

use Ctrb\Chatwork\Auth;
use Ctrb\User;
use Predis\Client;

class Callback extends Base
{

    public function execute()
    {
        /** @var Client $redis */
        $redis = $GLOBALS['redis'];
        $oauth = new Auth();
        $user  = new User();

        $h = 'htmlspecialchars';

        if (isset($_GET['code'])) {

            $code = $_GET['code'];

            $access_token = $oauth->authByCode($code);
            if (is_null($access_token)) {
                return <<<HTML
codeが不正です。認可をやり直してください
<hr>
<a href="index.php">index.php</a>
HTML;
            }

            $owner = $oauth->owner($access_token);
            $account_id = $owner->getAccountId();
            $refresh_token = $access_token->getRefreshToken();

            $redis->set("refresh:{$account_id}", $refresh_token);
            $user->login($account_id);

            $redis->hset("accoount:{$account_id}", 'name', $name = $owner->getName());
            $redis->hset("accoount:{$account_id}", 'icon', $icon = $owner->getAvatarImageUrl());

            return <<<HTML
<img src="{$h($icon)}"> {$h($name)} としてログインしました
<hr>
<a href="welcome.php">welcome.php</a>
HTML;
        }

        if ($user->isLoggedIn()) {
            $account_id = $user->getId();

            $refresh_token = $redis->get("refresh:{$account_id}");

            $access_token = $oauth->authRefresh($refresh_token);
            $refresh_token = $access_token->getRefreshToken();

            $redis->set("refresh:{$account_id}", $refresh_token);

            $icon = $redis->hget("accoount:{$account_id}", 'icon');
            $name = $redis->hget("accoount:{$account_id}", 'name');

            return <<<HTML
<img src="{$h($icon)}"> {$h($name)} としてログインしています
<hr>
<a href="welcome.php">welcome.php</a>
HTML;
        }

        return <<<HTML
ログインしていません。認可をやり直してください
<hr>
<a href="index.php">index.php</a>
HTML;
    }
}
