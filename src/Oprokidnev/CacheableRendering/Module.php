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

namespace Oprokidnev\CacheableRendering;

class Module implements \Zend\ModuleManager\Feature\ConfigProviderInterface
{

    /**
     * @param \Zend\ModuleManager\ModuleManager $moduleManager
     */
    public function init(\Zend\ModuleManager\ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();

        /**
         * Append lazy services config
         */
        $events->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG,
            [$this, 'onMergeConfig']);
    }

    /**
     *
     * @param \Zend\ModuleManager\ModuleEvent $e
     */
    public function onMergeConfig(\Zend\ModuleManager\ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config = $configListener->getMergedConfig(false);

        /**
         * Handle autoloaded config for a first time
         */
        $cacheConfigLocation = './config/autoload/cacheable-rendering.config.local.php';
        if (!\file_exists($cacheConfigLocation)) {
            \copy(__DIR__ . '/../../../config/cacheable-rendering.config.local.php', $cacheConfigLocation);

            if (!\file_exists('./data/cacheable-rendering')) {
                \mkdir('./data/cacheable-rendering');
            }
            
            $cacheConfig = require $cacheConfigLocation;

            $config = \Zend\Stdlib\ArrayUtils::merge($configListener->getMergedConfig(false),
                    $cacheConfig);
        }
        if (@$config['oprokidnev']['cacheable-rendering']['use_lazy_factories']) {
            $lazyServiceConfig = require __DIR__ . '/../../../config/lazy-services.module.php';
            $config            = \Zend\Stdlib\ArrayUtils::merge($configListener->getMergedConfig(false),
                    $lazyServiceConfig);
        }

        $configListener->setMergedConfig($config);
    }

    /**
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }
}
