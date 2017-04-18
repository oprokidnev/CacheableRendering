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

use Zend\View\Helper as ViewHelper;

abstract class TrackableContainer extends \Zend\View\Helper\Placeholder\Container
{
    public static $nameToContainerClass = [
        ViewHelper\HeadScript::class => TrackableContainer\HeadScript::class,
        ViewHelper\HeadLink::class => TrackableContainer\HeadLink::class,
        ViewHelper\HeadMeta::class => TrackableContainer\HeadMeta::class,
        ViewHelper\InlineScript::class => TrackableContainer\InlineScript::class,
        ViewHelper\HeadStyle::class => TrackableContainer\HeadStyle::class,
        ViewHelper\Placeholder::class => TrackableContainer\Placeholder::class,
    ];
    
    protected $placeholderName          = null;
    protected static $trackingEnabled   = [
        false
    ];
    
    protected static $tracker           = [];
    protected static $nesting           = 0;

    abstract public static function getServiceName();

    public static function startTracking()
    {
        static::$nesting++;
        static::$trackingEnabled[static::$nesting] = true;
        static::$tracker[static::$nesting]         = [];
    }

    public static function stopTracking()
    {
        static::$trackingEnabled[static::$nesting] = false;
        $result                                    = static::$tracker[static::$nesting];
        static::$nesting                           = static::$nesting === 0 ? 0 : static::$nesting
            - 1;
        return $result;
    }

    public function setPlaceholderName($placeholderName)
    {
        $this->placeholderName = $placeholderName;
        return $this;
    }

    public function append($value)
    {
        $this->track(__FUNCTION__, $value);
        return parent::append($value);
    }

    public function prepend($value)
    {
        $this->track(__FUNCTION__, $value);
        return parent::prepend($value);
    }

    public function set($value)
    {
        $this->track(__FUNCTION__, $value);
        return parent::set($value);
    }

    protected function track($name, $arguments)
    {
        foreach (\range(static::$nesting, 0, -1) as $nest) {
            if (static::$trackingEnabled[$nest]) {
                static::$tracker[$nest][\get_class($this)][] = [$name, $arguments,
                    $this->placeholderName];
            }
        }

        return $this;
    }
}
