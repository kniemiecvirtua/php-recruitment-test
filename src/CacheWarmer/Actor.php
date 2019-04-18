<?php
declare(strict_types=1);

namespace Snowdog\DevTest\CacheWarmer;

class Actor
{
    private $callable;

    public function setActor($callable) {
        $this->callable = $callable;
    }

    /**
     * @param string $hostname
     * @param string $ip
     * @param string $url
     */
    public function act(string $hostname, string $ip, string $url)
    {
        call_user_func($this->callable, $hostname, $ip, $url);
    }
}
