<?php

namespace Oprokidnev\CacheableRendering\View\Strategy;

/**
 * Description of CacheableStrategy
 *
 * @author oprokidnev
 */
class CacheStrategy extends \Zend\EventManager\AbstractListenerAggregate
{

    public static function createViaServiceManager(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $cacheRenderer = $serviceLocator->get(\Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer::class);
        return new static($cacheRenderer);
    }

    /**
     * @var \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    protected $renderer;

    /**
     * Constructor
     *
     * @param  \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer $renderer
     */
    public function __construct(\Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer $renderer)
    {
        $this->renderer = $renderer;
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
     * @param  \Zend\View\ViewEvent $e
     * @return \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer
     */
    public function selectRenderer(\Zend\View\ViewEvent $e)
    {
        if ($e->getModel() && $e->getModel()->getVariable(\Oprokidnev\CacheableRendering\View\Model\CacheModelInterface::CACHE_PARAMETER_NAME) !== null) {
            return $this->renderer;
        }
        return null;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param \Zend\View\ViewEvent $e
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
