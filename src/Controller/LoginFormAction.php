<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\Session;

class LoginFormAction
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function execute()
    {
        if ($this->session->isLoggedIn()) {
            header("HTTP/1.0 403 Forbidden");
            require __DIR__ . '/../view/403.phtml';
            exit;
        }

        require __DIR__ . '/../view/login.phtml';
    }
}