<?php

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\User;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand
{
    const SITEMAP_IMPORT_DIR = 'var/import/';

    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var Website
     */
    private $currentWebsite;

    public function __construct(
        WebsiteManager $websiteManager,
        PageManager $pageManager,
        UserManager $userManager
    ) {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->userManager = $userManager;
    }

    public function __invoke($path, $website_name, $user_id, OutputInterface $output)
    {
        $fullPath = $this->getSitemapFullPath($path);
        if (!file_exists($fullPath)) {
            $output->writeln('<error>Sitemap ' . $path . ' does not exist!</error>');
            return;
        }

        $user = $this->userManager->get($user_id);
        if (!$user) {
            $output->writeln('<error>User with ID' . $user_id . ' does not exist!</error>');
            return;
        }

        $content = file_get_contents($fullPath);
        $xml = new \SimpleXMLElement($content);
        foreach ($xml->url as $node) {
            $url = $node->loc;
            if (!$url) {
                continue;
            }

            $website = $this->getCurrentWebsite($user, $url);
            if (!$website) {
                $hostname = $this->getHostnameFromUrl($url);
                $websiteId = $this->websiteManager->create($user, $website_name, $hostname);

                if ($websiteId) {
                    $website = $this->websiteManager->getById($websiteId);
                }
            }

            if (!$website) {
                continue;
            }

            $path = $this->getPathFromUrl($url);
            $websiteId = $website->getWebsiteId();
            $page = $this->pageManager->getPageByWebsiteAndUrl($websiteId, $path);
            if (!$page) {
                if ($pageId = $this->pageManager->create($website, $path)) {
                    $output->writeln('Page ' . $pageId . ' has been created');
                }
            }
        }
    }

    private function getCurrentWebsite(User $user, $url)
    {
        if (!$this->currentWebsite) {
            $hostname = $this->getHostnameFromUrl($url);
            if (null !== $hostname) {
                $this->currentWebsite = $this->websiteManager->getWebsiteByUserAndHost($user, $hostname);
            }
        }

        return $this->currentWebsite;
    }

    private function getSitemapFullPath($path)
    {
        return __DIR__ . '/../../' . self::SITEMAP_IMPORT_DIR . $path;
    }

    private function getHostnameFromUrl($url)
    {
        $urlParts = parse_url($url);

        return (!empty($urlParts['host'])) ? $urlParts['host'] : null;
    }

    private function getPathFromUrl($url)
    {
        $urlParts = parse_url($url);

        return (!empty($urlParts['path'])) ? ltrim($urlParts['path'], DIRECTORY_SEPARATOR) : null;
    }
}
