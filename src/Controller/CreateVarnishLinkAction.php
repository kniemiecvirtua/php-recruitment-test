<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;

class CreateVarnishLinkAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /** @var User */
    private $user;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager, WebsiteManager $websiteManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
        $this->websiteManager = $websiteManager;
    }

    public function execute()
    {
        if (isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);
        }
        $varnish = $_POST['varnish'];
        $website = $_POST['website'];
        if (!$this->user || empty($varnish) || empty($website)) {
            echo 'error';
        }

        $userId = $this->user->getUserId();
        if (!$this->websiteManager->isUserWebsite($userId, $website)) {
            echo 'error';
        }

        $isActive = $this->varnishManager->getIsActiveByWebsiteAndVarnish($varnish, $website);
        if ($isActive) {
            $this->varnishManager->unlink($varnish, $website);
        } else {
            $this->varnishManager->link($varnish, $website);
        }

        echo 'ok';
    }
}
