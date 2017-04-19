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

return [
    'service_manager' => [
        'factories' => [
            View\Renderer\CacheRenderer::class => [
                View\Renderer\CacheRenderer::class, 'createViaServiceContainer'
            ],
            View\Strategy\CacheStrategy::class => [
                View\Strategy\CacheStrategy::class, 'createViaServiceContainer'
            ],
            Options\ModuleOptions::class       => [
                Options\ModuleOptions::class, 'createViaServiceContainer'
            ],
            Cache\StorageManager::class        => [
                Cache\StorageManager::class, 'createViaServiceContainer'
            ],
        ],
    ],
    'view_manager'    => [
        'strategies' => [
            \Oprokidnev\CacheableRendering\View\Strategy\CacheStrategy::class
        ],
    ],
    'view_helpers'    => [
        'factories'  => [
            View\Helper\Callback::class => [View\Helper\Callback::class, 'createViaServiceContainer'],
            View\Helper\Partial::class  => [View\Helper\Partial::class, 'createViaServiceContainer'],
            View\Helper\Capture::class  => [View\Helper\Capture::class, 'createViaServiceContainer'],
        ],
        'aliases'    => [
            'cachedCallback' => View\Helper\Callback::class,
            'cachedPartial'  => View\Helper\Partial::class,
            'cachedCapture'  => View\Helper\Capture::class,
        ],
        'delegators' => [
            \Zend\View\Helper\HeadScript::class  => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            \Zend\View\Helper\HeadLink::class     => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            \Zend\View\Helper\HeadStyle::class   => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            \Zend\View\Helper\HeadMeta::class    => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            \Zend\View\Helper\InlineScript::class => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            \Zend\View\Helper\Placeholder::class  => [
                View\Helper\Placeholder\PlaceholderDelegatorFactory::class,
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class,
            ],
        ],
    ],
];
