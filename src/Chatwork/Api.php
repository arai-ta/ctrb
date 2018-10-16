<?php

namespace Ctrb\Chatwork;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Token\AccessToken;
use Monolog\Logger;

class Api
{

    const URL = 'https://api.chatwork.com/v2/';

    /** @var Logger */
    private $logger;

    /** @var mixed */
    private $token;

    public function __construct($token = null, $logger = null)
    {
        $this->token = $token;
        $this->logger = $logger ?: $GLOBALS['logger'];
    }

    public function getMyTasks()
    {
        return $this->request('GET', 'my/tasks');
    }

    private function request($meth, $path)
    {
        try {
            $req = new Request($meth, self::URL.$path);

            if ($this->token instanceof AccessToken) {
                $req = $req->withHeader('Authorization', sprintf('Bearer %s', $this->token));
            } else {
                $req = $req->withHeader('X-ChatWorkToken', $this->token);
            }

            $client = new Client();

            $response = $client->send($req);

            return $response->getBody()->getContents();

        } catch (GuzzleException $e) {

            $this->logger->error($e->getMessage());

            return false;
        }

    }
}


