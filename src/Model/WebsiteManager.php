<?php
declare(strict_types=1);

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class WebsiteManager
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    public function getById($websiteId)
    {
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM websites WHERE website_id = :id');
        $query->setFetchMode(\PDO::FETCH_CLASS, Website::class);
        $query->bindParam(':id', $websiteId, \PDO::PARAM_STR);
        $query->execute();
        /** @var Website $website */
        $website = $query->fetch(\PDO::FETCH_CLASS);
        return $website;
    }

    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM websites WHERE user_id = :user');
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    public function create(User $user, $name, $hostname)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO websites (name, hostname, user_id) VALUES (:name, :host, :user)');
        $statement->bindParam(':name', $name, \PDO::PARAM_STR);
        $statement->bindParam(':host', $hostname, \PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     * @param int $user
     * @param int $website
     * @return bool
     */
    public function isUserWebsite(int $user, int $website): bool
    {
        $query = $this->database->prepare(
            'SELECT website_id FROM websites WHERE user_id = :user_id AND website_id = :website_id'
        );
        $query->bindParam(':user_id', $user, \PDO::PARAM_INT);
        $query->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $query->execute();

        return (bool)$query->fetchColumn();
    }

    /**
     * @param User $user
     * @param string $hostname
     * @return Website
     */
    public function getWebsiteByUserAndHost(User $user, string $hostname)
    {
        $userId = $user->getUserId();
        $query = $this->database->prepare(
            'SELECT * FROM websites WHERE user_id = :user_id AND hostname = :hostname'
        );
        $query->setFetchMode(\PDO::FETCH_CLASS, Website::class);
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->bindParam(':hostname', $hostname, \PDO::PARAM_STR);
        $query->execute();

        $website = $query->fetch(\PDO::FETCH_CLASS);
        return $website;
    }

}