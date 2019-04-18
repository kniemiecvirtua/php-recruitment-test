<?php
declare(strict_types=1);

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     * @param int $websiteId
     * @param string $url
     * @return Page
     */
    public function getPageByWebsiteAndUrl(int $websiteId, string $url)
    {
        $query = $this->database->prepare(
            'SELECT * FROM pages WHERE website_id = :website_id AND url = :url'
        );
        $query->bindParam(':website_id', $websiteId, \PDO::PARAM_INT);
        $query->bindParam(':url', $url, \PDO::PARAM_STR);
        $query->execute();

        return $query->fetchObject(Page::class);
    }

    /**
     * @param int $pageId
     */
    public function setLastVisit(int $pageId): void
    {
        $lastVisit = date(self::DATETIME_FORMAT);
        $statement = $this->database->prepare(
            'UPDATE pages SET last_visit = :last_visit WHERE page_id = :page_id'
        );
        $statement->bindParam(':last_visit', $lastVisit, \PDO::PARAM_STR);
        $statement->bindParam(':page_id', $pageId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * @param int $pageId
     */
    public function increaseViewedCount(int $pageId): void
    {
        $statement = $this->database->prepare(
            'UPDATE pages SET viewed = viewed + 1 WHERE page_id = :page_id'
        );
        $statement->bindParam(':page_id', $pageId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Retrieves total number of pages assigned to the given user
     *
     * @param int $userId
     * @return int
     */
    public function getTotalNumberUserPages(int $userId)
    {
        $query = $this->database->prepare(
            'SELECT count(page_id) FROM pages JOIN websites ON websites.website_id = pages.website_id WHERE websites.user_id = :user_id'
        );
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();

        return (int)$query->fetchColumn();
    }

    /**
     * Retrieves last visited page
     *
     * @param int $userId
     * @return Page
     */
    public function getLastVisitedPage(int $userId)
    {
        $query = $this->database->prepare(
            'SELECT pages.url FROM pages 
JOIN websites ON websites.website_id = pages.website_id 
WHERE websites.user_id = :user_id 
ORDER BY last_visit DESC 
LIMIT 1'
        );
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchObject(Page::class);
    }

    /**
     * Retrieves the most visited page
     *
     * @param int $userId
     * @return Page
     */
    public function getMostVisitedPage(int $userId)
    {
        $query = $this->database->prepare(
            'SELECT pages.url FROM pages 
JOIN websites ON websites.website_id = pages.website_id 
WHERE websites.user_id = :user_id 
ORDER BY viewed DESC 
LIMIT 1'
        );
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchObject(Page::class);
    }
}