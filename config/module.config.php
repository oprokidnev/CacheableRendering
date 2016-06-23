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

namespace Oprokidnev\CacheableRendering;

return [
    'service_manager' => [
        'factories' => [
            \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer::class => [
                \Oprokidnev\CacheableRendering\View\Renderer\CacheRenderer::class, 'createViaServiceManager'
            ],
            \Oprokidnev\CacheableRendering\View\Strategy\CacheStrategy::class => [
                \Oprokidnev\CacheableRendering\View\Strategy\CacheStrategy::class, 'createViaServiceManager'
            ],
        ],
    ],
    'view_manager'=>[
        'strategies'=>[
            \Oprokidnev\CacheableRendering\View\Strategy\CacheStrategy::class
        ],
    ],
    'view_helpers'    => [
        'factories'  => [
            'cachedCallback' => [ View\Helper\Callback::class, 'createViaViewHelperManager'],
            'cachedPartial'  => [ View\Helper\Partial::class, 'createViaViewHelperManager'],
        ],
        'delegators' => [
            'headScript'   => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            'headLink'     => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            'headStyle'    => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            'headMeta'     => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            'inlineScript' => [
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class
            ],
            'placeholder'  => [
                View\Helper\Placeholder\PlaceholderDelegatorFactory::class,
                View\Helper\Placeholder\TrackableContainerDelegatorFactory::class,
            ],
        ],
    ],
];
