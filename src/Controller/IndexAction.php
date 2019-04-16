<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;

class IndexAction
{
    const NOT_SET_VALUE = 'value is not set';

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PageManager
     */
    private $pageManager;

    public function __construct(
        UserManager $userManager,
        WebsiteManager $websiteManager,
        PageManager $pageManager
    ) {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        if (isset($_SESSION['login'])) {
            $this->user = $userManager->getByLogin($_SESSION['login']);
        }
    }

    protected function getWebsites()
    {
        if($this->user) {
            return $this->websiteManager->getAllByUser($this->user);
        } 
        return [];
    }

    protected function getTotalPagesNumber()
    {
        if (null !== $this->user) {
            return $this->pageManager->getTotalNumberUserPages($this->user->getUserId());
        }

        return null;
    }

    protected function getLastVisitedPageUrl()
    {
        if (null !== $this->user) {
            $page = $this->pageManager->getLastVisitedPage($this->user->getUserId());
            return $page ? $page->getUrl() : self::NOT_SET_VALUE;
        }

        return null;
    }

    protected function getMostVisitedPage()
    {
        if (null !== $this->user) {
            $page = $this->pageManager->getMostVisitedPage($this->user->getUserId());
            return $page ? $page->getUrl() : self::NOT_SET_VALUE;
        }

        return null;
    }

    public function execute()
    {
        require __DIR__ . '/../view/index.phtml';
    }
}