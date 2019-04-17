<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * @var User
     */
    private $user;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $ip = $_POST['ip'];

        if (!empty($ip) && isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);

            if ($this->varnishManager->create($this->user, $ip)) {
                $_SESSION['flash'] = 'Varnish server created.';
            }
        }

        $_SESSION['flash'] = 'Varnish server cannot be created.';

        header('Location: /varnishes');
    }
}