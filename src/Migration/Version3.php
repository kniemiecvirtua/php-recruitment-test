<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version3
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(
        Database $database
    ) {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->alterColumnToPageTable();
    }

    private function alterColumnToPageTable()
    {
        $alterQuery = <<<SQL
ALTER TABLE `pages` 
ADD COLUMN `last_visit` datetime NULL,
ADD COLUMN `viewed` int(11) NOT NULL DEFAULT 0;
SQL;
        $this->database->exec($alterQuery);
    }
}
