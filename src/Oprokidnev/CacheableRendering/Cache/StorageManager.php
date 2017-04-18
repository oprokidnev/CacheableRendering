<?php

/*
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Oprokidnev\CacheableRendering\Cache;

/**
 * Stores module cache adapters
 *
 * @author oprokidnev
 */
class StorageManager
{

    /**
     *
     * @var \Oprokidnev\CacheableRendering\Options\ModuleOptions
     */
    protected $moduleOptions = null;

    protected $adapters = [];

    /**
     *
     * @param \Oprokidnev\CacheableRendering\Options\ModuleOptions $moduleOptions
     */
    public function __construct(\Oprokidnev\CacheableRendering\Options\ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }

    public static function createViaServiceContainer(\Psr\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        return new static($container->get(\Oprokidnev\CacheableRendering\Options\ModuleOptions::class));
    }

    public function getAdapter($serviceName)
    {
        $adapters = $this->moduleOptions->getAdapters();
        if (!\array_key_exists($serviceName, $this->adapters) && !\array_key_exists($serviceName, $adapters)) {
            throw new \Exception(\sprintf('Can\'t find proper adapter for servivce %s', $serviceName));
        }

        if (!\array_key_exists($serviceName, $this->adapters)) {
            $this->adapters[$serviceName] = \Zend\Cache\StorageFactory::factory($adapters[$serviceName]);
        }

        return $this->adapters[$serviceName];
    }

    public function clearByTags($tags)
    {
        foreach ($this->moduleOptions->getAdapters() as $serviceName => $arapterConfig) {
            $adapter=$this->getAdapter($serviceName);
            if ($adapter instanceof \Zend\Cache\Storage\TaggableInterface) {
                $adapter->clearByTags((array) $tags, false);
            }
        }
    }
}
