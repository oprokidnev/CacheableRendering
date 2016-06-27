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

namespace Oprokidnev\CacheableRendering\View\Renderer;


/**
 * CacheRenderer
 *
 * @author oprokidnev
 */
class CacheRenderer implements \Zend\View\Renderer\RendererInterface
{

    /**
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cacheStorage = null;

    /**
     *
     * @var \Zend\View\View
     */
    protected $view                    = null;

    /**
     *
     * @var \Zend\ServiceManager\AbstractPluginManager
     */
    protected $viewHelperPluginManager = null;

    public static function createViaServiceManager(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $config        = $serviceLocator->get('config');
        $storageConfig = $config['oprokidnev']['cacheable-rendering']['adapters'][__CLASS__];
        return new static(\Zend\Cache\StorageFactory::factory($storageConfig), $serviceLocator->get(\Zend\View\View::class), $serviceLocator->get('ViewHelperManager'));
    }

    public function __construct($cacheStorage, $view, $viewHelperPluginManager)
    {
        $this->cacheStorage            = $cacheStorage;
        $this->view                    = clone $view;
        $this->viewHelperPluginManager = $viewHelperPluginManager;
    }

    public function getEngine()
    {
        return;
    }

    public function render($nameOrModel, $values = null)
    {
        if ($nameOrModel instanceof \Zend\View\Model\ModelInterface && ($cacheKey = $nameOrModel->getVariable(\Oprokidnev\CacheableRendering\View\Model\CacheModelInterface::CACHE_PARAMETER_NAME)) !== null) {

            /**
             * Prevent loop renderer use.
             */
            $nameOrModel->setVariable(\Oprokidnev\CacheableRendering\View\Model\CacheModelInterface::CACHE_PARAMETER_NAME, null);

            if ($this->cacheStorage->hasItem($cacheKey)) {
                $result = $this->cacheStorage->getItem($cacheKey);
                return $result($this->viewHelperPluginManager);
            }

            \Oprokidnev\CacheableRendering\View\Helper\Placeholder\TrackableContainer::startTracking();

            $renderer = null;

            $callbackHandler = $this->view->getEventManager()
                ->attach(\Zend\View\ViewEvent::EVENT_RENDERER_POST, function (\Zend\View\ViewEvent $event) use (&$renderer, &$callbackHandler) {
                $renderer = $event->getRenderer();
                $this->view->getEventManager()->detach($callbackHandler);
            });

            $nameOrModel->setOption('has_parent', true);
            $result = $this->view->render($nameOrModel);


            $this->cacheStorage->setItem($cacheKey, Result::factory($result, \Oprokidnev\CacheableRendering\View\Helper\Placeholder\TrackableContainer::stopTracking(), get_class($renderer)));

            return $result;
        } else {
            dump('Cant render model by templat location');
        }
    }

    protected $resolver = null;

    /**
     * 
     * @param \Zend\View\Resolver\ResolverInterface $resolver
     * @return \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    public function setResolver(\Zend\View\Resolver\ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }
}
