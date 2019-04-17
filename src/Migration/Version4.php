<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\WebsiteManager;

class Version4
{
    /**
     * @var Database|\PDO
     */
    private $database;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(
        Database $database,
        UserManager $userManager,
        WebsiteManager $websiteManager,
        PageManager $pageManager,
        VarnishManager $varnishManager
    ) {
        $this->database = $database;
        $this->userManager = $userManager;
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->varnishManager = $varnishManager;
    }

    public function __invoke()
    {
        $this->createVarnishTable();
        $this->addVarnishAndWebsiteRelationTable();
        $this->addVarnishData();
    }

    private function createVarnishTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes` (
  `varnish_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL UNIQUE,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`varnish_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `varnish_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function addVarnishAndWebsiteRelationTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnish_website` (
  `varnish_id` int(11) unsigned NOT NULL,
  `website_id`  int(11) unsigned NOT NULL,
  PRIMARY KEY (`varnish_id`, `website_id`),
  CONSTRAINT `varnish_id_fk` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`varnish_id`),
  CONSTRAINT `website_id_fk` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function addVarnishData()
    {
        $data = [
            'test' => '122.22.1.1',
            'example' => '144.12.2.5',
            'demo' => '155.232.5.5',
            'dev' => '153.34.2.0',
            'not_exist' => '153.34.1.0',
        ];

        foreach ($data as $login => $ip) {
            $user = $this->userManager->getByLogin($login);
            if (!$user) {
                continue;
            }
            $this->varnishManager->create($user, $ip);
        }
    }
}