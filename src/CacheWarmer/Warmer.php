<?php
declare(strict_types=1);

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
    public function setActor(Actor $actor): void
    {
        $this->actor = $actor;
    }

    /**
     * @param string $hostname
     */
    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    /**
     * @param ResolverInterface $resolver
     */
    public function setResolver(ResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }

    /**
     * @param string $url
     */
    public function warm(string $url): void
    {
        $ip = $this->resolver->getIp($this->hostname);
        sleep(1); // this emulates visit to http://$hostname/$url via $ip
        $this->actor->act($this->hostname, $ip, $url);
    }
}
