<?php

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Symfony\Component\Console\Output\OutputInterface;
use Snowdog\DevTest\CacheWarmer;

class WarmCommand
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var CacheWarmer\Resolver
     */
    private $resolver;

    /**
     * @var CacheWarmer\Actor
     */
    private $actor;

    /**
     * @var CacheWarmer\Warmer
     */
    private $warmer;

    public function __construct(
        WebsiteManager $websiteManager,
        PageManager $pageManager,
        CacheWarmer\Resolver $resolver,
        CacheWarmer\Actor $actor,
        CacheWarmer\Warmer $warmer

    ) {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->resolver = $resolver;
        $this->actor = $actor;
        $this->warmer = $warmer;
    }

    public function __invoke($id, OutputInterface $output)
    {
        $website = $this->websiteManager->getById($id);
        if ($website) {
            $pages = $this->pageManager->getAllByWebsite($website);

            $resolver = $this->resolver;
            $actor = $this->actor;
            $actor->setActor(function ($hostname, $ip, $url) use ($output) {
                $output->writeln('Visited <info>http://' . $hostname . '/' . $url . '</info> via IP: <comment>' . $ip . '</comment>');
            });
            $warmer = $this->warmer;
            $warmer->setResolver($resolver);
            $warmer->setHostname($website->getHostname());
            $warmer->setActor($actor);

            foreach ($pages as $page) {
                $warmer->warm($page->getUrl());
                $this->updatePageStats($page->getPageId());
            }
        } else {
            $output->writeln('<error>Website with ID ' . $id . ' does not exists!</error>');
        }
    }

    private function updatePageStats($pageId)
    {
        $this->pageManager->setLastVisit($pageId);
        $this->pageManager->increaseViewedCount($pageId);
    }
}