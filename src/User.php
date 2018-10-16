<?php

namespace Ctrb;

class User
{

    private $session;

    public function __construct($session = [])
    {
        $this->session = $session ?: $_SESSION;
    }

    public function isLoggedIn()
    {
        return isset($this->session['account_id'])
            && 0 < $this->session['account_id'];
    }

    public function getId()
    {
        return $this->session['account_id'];
    }

    public function login($account_id)
    {
        $_SESSION['account_id'] = $account_id;
    }
}
