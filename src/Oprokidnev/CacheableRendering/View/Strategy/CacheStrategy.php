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

namespace Oprokidnev\CacheableRendering\View\Strategy;

/**
 * CacheStrategy
 *
 * @author oprokidnev
 */
class CacheStrategy extends \Zend\EventManager\AbstractListenerAggregate
{

    /**
     * @var \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    protected $renderer;

    /**
     * Constructor
     *
     * @param \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer $renderer
     */
    public function __construct(\Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public static function createViaServiceContainer(\Psr\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $cacheRenderer = $container->get(\Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer::class);
        return new static($cacheRenderer);
    }

    /**
     * Retrieve the composed renderer
     *
     * @return \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(\Zend\EventManager\EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(\Zend\View\ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
        $this->listeners[] = $events->attach(\Zend\View\ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
    }

    /**
     * Select the PhpRenderer; typically, this will be registered last or at
     * low priority.
     *
     * @param \Zend\View\ViewEvent $e
     *
     * @return \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    public function selectRenderer(\Zend\View\ViewEvent $e)
    {
        if ($e->getModel() && $e->getModel()->getVariable(\Oprokidnev\CacheableRendering\View\Model\CacheModelInterface::CACHE_PARAMETER_NAME) !== null) {
            return $this->renderer;
        }
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param \Zend\View\ViewEvent $e
     *
     * @return void
     */
    public function injectResponse(\Zend\View\ViewEvent $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }

        $result   = $e->getResult();
        $response = $e->getResponse();

        // Set content
        // If content is empty, check common placeholders to determine if they are
        // populated, and set the content from them.
        if (empty($result)) {
            $placeholders = $renderer->plugin('placeholder');
            foreach ($this->contentPlaceholders as $placeholder) {
                if ($placeholders->containerExists($placeholder)) {
                    $result = (string) $placeholders->getContainer($placeholder);
                    break;
                }
            }
        }
        $response->setContent($result);
    }
}
