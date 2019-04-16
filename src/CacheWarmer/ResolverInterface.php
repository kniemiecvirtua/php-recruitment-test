<?php

namespace Snowdog\DevTest\CacheWarmer;

interface ResolverInterface
{
    public function getIp($hostname);
}
