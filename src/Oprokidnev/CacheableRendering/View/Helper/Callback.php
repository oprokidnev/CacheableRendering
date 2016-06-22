<?php

/*
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

    public static function createViaViewHelperManager(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('config');
        $storageConfig = $config['oprokidnev']['cacheable-rendering']['adapters'][__CLASS__];
        return new static(\Zend\Cache\StorageFactory::factory($storageConfig));
    }

    /**
     *
     * @var \Zend\Cache\Storage\AbstractAdapter
     */
    protected $cacheStorage;

    /**
     *
     * @param \Zend\Cache\Storage\AbstractAdapter $cacheStorage
     */
    public function __construct(\Zend\Cache\Storage\Adapter\AbstractAdapter $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public function __invoke($key, callable $callback, $groups = ['database'])
    {
        if ($key === null) {
            throw new \Exception('Cached partial rendering is not avaliable without a cache key.');
        }
        $key = md5($key.strtolower(__CLASS__));

        if ($this->cacheStorage->hasItem($key)) {
            $result = $this->cacheStorage->getItem($key);
            return $result($this->getView());
        }
        ob_start();
        Placeholder\TrackableContainer::startTracking();
        $result = (string) $callback();
        $result.= ob_get_clean();
        $this->cacheStorage->setItem($key, Result::factory($result, Placeholder\TrackableContainer::stopTracking()));
        return $result;
    }
}
