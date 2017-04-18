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


namespace Oprokidnev\CacheableRendering\View\Helper\Placeholder;

class TrackableContainerDelegatorFactory implements \Zend\ServiceManager\Factory\DelegatorFactoryInterface
{
    public function __invoke(\Interop\Container\ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $instance = $callback();
        /* @var $instance \Zend\View\Helper\Placeholder\Container\AbstractStandalone|\Zend\View\Helper\Placeholder */
        if (($instance instanceof \Zend\View\Helper\Placeholder\Container\AbstractStandalone || $instance instanceof \Zend\View\Helper\Placeholder) && isset(TrackableContainer::$nameToContainerClass[$name])) {
            $className = TrackableContainer::$nameToContainerClass[$name];
            if (\method_exists($instance, 'setContainerClass')) {
                $instance->setContainerClass($className);
            }
            if (\method_exists($instance, 'setContainer')) {
                $instance->setContainer(new $className());
            }

            if (\method_exists($instance, 'getContainerClass')) {
            }
        }
        return $instance;
    }
}
