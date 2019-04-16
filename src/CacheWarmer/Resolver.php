<?php

namespace Snowdog\DevTest\CacheWarmer;

class Resolver implements ResolverInterface
{
    public function getIp($hostname)
    {
        return gethostbyname($hostname);
    }
}
