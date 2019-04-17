<?php

namespace Snowdog\DevTest\Model;

class Session
{
    private $isLoggedIn;

    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function isLoggedIn()
    {
        if (null === $this->isLoggedIn) {
            $this->isLoggedIn = !!$this->userManager->getByLogin($_SESSION['login']);
        }

        return $this->isLoggedIn;
    }
}
