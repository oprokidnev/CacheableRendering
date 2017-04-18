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
 * Container for callback cache view helper
 *
 * @author oprokidnev
 */
class Result
{
    /**
     *
     * @var string
     */
    protected $result;

    /**
     *
     * @var array
     */
    protected $placeholderCalls = null;

    /**
     *
     * @param string $result
     * @param array  $placeholderCalls
     */
    public function __construct($result, $placeholderCalls)
    {
        $this->placeholderCalls = $placeholderCalls;
        if (!$this->is_iterable($placeholderCalls)) {
            $this->placeholderCalls = [];
        }
        $this->result = $result;
    }

    /**
     *
     * @param \Zend\View\Renderer\RendererInterface $view
     *
     * @return string
     */
    public function __invoke(\Zend\View\Renderer\RendererInterface $view)
    {
        foreach ($this->placeholderCalls as $containerClass => $calls) {
            foreach ($calls as $call) {
                list($method, $args, $scope) = $call;
                if (\method_exists($containerClass, 'getServiceName')) {
                    $standalonePlaceholder = $view->plugin($containerClass::getServiceName());
                    /* @var $standalonePlaceholder \Zend\View\Helper\Placeholder\Container\AbstractStandalone */
                    \call_user_func([$standalonePlaceholder->getContainer($scope),
                        $method], $args);
                }
            }
        };
        return $this->result;
    }

    /**
     *
     * @param string $result
     * @param array  $placeholderCalls
     *
     * @return static
     */
    public static function factory($result, $placeholderCalls)
    {
        return new static($result, $placeholderCalls);
    }

    /**
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     *
     * @return array
     */
    public function getPlaceholderCalls()
    {
        return $this->placeholderCalls;
    }

    /**
     *
     * @param string $result
     *
     * @return static
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     *
     * @param array $placeholderCalls
     *
     * @return static
     */
    public function setPlaceholderCalls($placeholderCalls)
    {
        $this->placeholderCalls = $placeholderCalls;
        return $this;
    }

    /**
     *
     * @param mixed $var
     *
     * @return bool
     */
    protected function is_iterable($var)
    {
        return (\is_array($var) || $var instanceof Traversable);
    }
}
