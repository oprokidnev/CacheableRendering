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


namespace Oprokidnev\CacheableRendering\View\Helper;

/**
 * Helps us easyly cache html output
 *
 * Usage:
 * ```php
 * <?php
 *
 * $key = 'some_env_key';
 * $this->cachedCallback($key, function () use ($serviceFromController) {
 *      foreach($serviceFromController->getItems() as $item):
 *      ?>
 *      <div class="item"><?= $item->getName()?></div>
 *      <?php endforeach; ?>
 *       <?php
 *      return 'Some fiish message';
 * })
 * ```
 *
 * @author oprokidnev
 */
class Callback extends \Zend\View\Helper\AbstractHelper
{

    /**
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cacheStorage;

    /**
     *
     * @param \Zend\Cache\Storage\StorageInterface $cacheStorage
     */
    public function __construct(\Zend\Cache\Storage\StorageInterface $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public function __invoke($key, callable $callback, $cacheTags = ['default'])
    {
        if ($key === null) {
            throw new \Exception('Cached partial rendering is not avaliable without a cache key.');
        }
        
        $key = \md5($key.\strtolower(__CLASS__));

        if ($this->cacheStorage->hasItem($key)) {
            $result = $this->cacheStorage->getItem($key);
            return $result($this->getView());
        }
        \ob_start();
        Placeholder\TrackableContainer::startTracking();
        
        $cacheTags = new \Zend\Stdlib\ArrayObject($cacheTags);
        $result = \strval($callback($cacheTags));
        $result.= \ob_get_clean();
        
        $this->cacheStorage->setItem($key, Result::factory($result, Placeholder\TrackableContainer::stopTracking()));
        
        if ($cacheTags->count()  && $this->cacheStorage instanceof \Zend\Cache\Storage\TaggableInterface) {
            $this->cacheStorage->setTags($key, \array_unique($cacheTags->getArrayCopy()));
        }
        
        return $result;
    }

    public static function createViaServiceContainer(\Psr\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $storageManager = $container->get(\Oprokidnev\CacheableRendering\Cache\StorageManager::class);
        /* @var $storageConfig \Oprokidnev\CacheableRendering\Cache\StorageManager::class */
        
        
        $adapter = $storageManager->getAdapter($requestedName);
        return new static($adapter);
    }
}
