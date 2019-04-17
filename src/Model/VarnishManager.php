<?php

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

    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        $query = $this->database->prepare(
            'SELECT * FROM varnishes WHERE user_id = :user_id'
        );
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function getWebsites(Varnish $varnish)
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

    public function getByWebsite(Website $website)
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

    public function getIsActiveByWebsiteAndVarnish($varnish, $website)
    {
        $query = $this->database->prepare(
            'SELECT varnish_id FROM varnish_website WHERE varnish_id = :varnish_id AND website_id = :website_id'
        );
        $query->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $query->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $query->execute();

        return (bool)$query->fetchColumn();
    }

    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (:ip, :user_id)');
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->database->lastInsertId();
    }

    public function link($varnish, $website)
    {
        $statement = $this->database->prepare(
            'INSERT INTO varnish_website (varnish_id, website_id) VALUES (:varnish_id, :website_id)'
        );
        $statement->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $statement->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function unlink($varnish, $website)
    {
        $statement = $this->database->prepare(
            'DELETE FROM varnish_website WHERE website_id = :website_id AND varnish_id = :varnish_id'
        );
        $statement->bindParam(':website_id', $website, \PDO::PARAM_INT);
        $statement->bindParam(':varnish_id', $varnish, \PDO::PARAM_INT);
        $statement->execute();
    }
}
