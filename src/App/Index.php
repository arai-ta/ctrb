<?php

namespace Ctrb\App;

use Ctrb\Chatwork\Auth;
use Ctrb\User;

class Index extends Base
{

    public function execute()
    {

        $user = new User();

        if ($user->isLoggedIn()) {

            $id = $user->getId();

            return <<<HTML
account_id:{$id}としてログイン済みです
<hr>
<a href="welcome.php">welcome.php</a>
HTML;
        }

        $oauth = new Auth();
        $url = $oauth->getConsentUrl();

        return <<<HTML
ログイン前ページ。下記コンセント画面から権限認可してください
<hr>
<a href="{$url}">{$url}</a>
HTML;
    }
}
