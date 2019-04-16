<?php

namespace Snowdog\DevTest\CacheWarmer;

class Warmer
{
    /** @var Actor */
    private $actor;
    /** @var ResolverInterface */
    private $resolver;
    /** @var string */
    private $hostname;

    /**
     * @param Actor $actor
     */
    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param ResolverInterface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    public function warm($url) {
        $ip = $this->resolver->getIp($this->hostname);
        sleep(1); // this emulates visit to http://$hostname/$url via $ip
        $this->actor->act($this->hostname, $ip, $url);
    }
}
