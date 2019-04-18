<?php
declare(strict_types=1);

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param User $user
     * @return Varnish[]
     */
    public function getAllByUser(User $user): array
    {
        $userId = $user->getUserId();
        $query = $this->database->prepare(
            'SELECT * FROM varnishes WHERE user_id = :user_id'
        );
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param Varnish $varnish
     * @return Website[]
     */
    public function getWebsites(Varnish $varnish): array
    {
        $varnishId = $varnish->getVarnishId();
        $query = $this->database->prepare(
            'SELECT websites.* FROM websites
 JOIN varnish_website ON varnish_website.website_id = websites.website_id 
 WHERE varnish_website.varnish_id = :varnish_id'
        );
        $query->bindParam(':varnish_id', $varnishId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    /**
     * @param Website $website
     * @return Varnish[]
     */
    public function getByWebsite(Website $website): array
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare(
            'SELECT varnishes.* FROM varnishes
 JOIN varnish_website ON varnish_website.varnish_id = varnishes.varnish_id
 WHERE websites.website_id = :website'
        );
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param int $varnish
     * @param int $website
     * @return bool
     */
    public function getIsActiveByWebsiteAndVarnish(int $varnish, int $website): bool
    {
        $query = $this->database->prepare(
            'SELECT varnish_id FROM varnish_website WHERE varnish_id = :varnish_id AND website_id = :website_id'
        );
        $query->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $query->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $query->execute();

        return (bool)$query->fetchColumn();
    }

    /**
     * @param User $user
     * @param string $ip
     * @return int
     */
    public function create(User $user, string $ip): int
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (:ip, :user_id)');
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $statement->execute();

        return (int)$this->database->lastInsertId();
    }

    /**
     * @param int $varnish
     * @param int $website
     */
    public function link(int $varnish, int $website): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO varnish_website (varnish_id, website_id) VALUES (:varnish_id, :website_id)'
        );
        $statement->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $statement->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * @param int $varnish
     * @param int $website
     */
    public function unlink(int $varnish, int $website): void
    {
        $statement = $this->database->prepare(
            'DELETE FROM varnish_website WHERE website_id = :website_id AND varnish_id = :varnish_id'
        );
        $statement->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $statement->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $statement->execute();
    }
}
