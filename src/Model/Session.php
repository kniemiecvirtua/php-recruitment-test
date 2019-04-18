<?php
declare(strict_types=1);

namespace Snowdog\DevTest\Model;

class Session
{
    /**
     * @var bool
     */
    private $isLoggedIn;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        if (null === $this->isLoggedIn) {
            $this->isLoggedIn = !!$this->userManager->getByLogin($_SESSION['login']);
        }

        return $this->isLoggedIn;
    }
}
