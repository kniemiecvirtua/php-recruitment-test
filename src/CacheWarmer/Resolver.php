<?php
declare(strict_types=1);

namespace Snowdog\DevTest\CacheWarmer;

class Resolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIp(string $hostname): string
    {
        return gethostbyname($hostname);
    }
}
