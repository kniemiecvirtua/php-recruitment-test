<?php
declare(strict_types=1);

namespace Snowdog\DevTest\CacheWarmer;

interface ResolverInterface
{
    /**
     * @param string $hostname
     * @return string
     */
    public function getIp(string $hostname): string;
}
