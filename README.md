# Cacheable placeholder module

Provides a set of utils for html caching including placeholder view helper calls interception and restore.

## Installation

```
composer require oprokidnev/cacheable-rendering
```

Initialize module `'Oprokidnev\\CacheableRendering'` in application.config.php and run you application once. Then you will get a new file in config/autoload named `cacheable-rendering.config.local.php`. This is the config with storage settings. By default I choose use filesystem cache adapter, but you can change this settings with the compatible with \Zend\Cache\StorageFactory::factory method array.

## View helpers

### CachedCallback

This is the most typical example when you should use callback cache.

```php
<?php
/*
 * Returned info is url dependant
 */
$key = $_SERVER['REQUEST_URI'];

/*
 * Cache big array rendering operation into html.
 */
$this->cachedCallback($key, function () use ($serviceFromController) {
    <?php foreach($veryBigIteratorWithLazyLoading->getItems() as $item): ?>
        <div class="item"><?= $item->getName()?></div>
    <?php endforeach; ?>
    <?php $this->placeholder('modals')->captureStart(); ?>
    <div class="modal">
        <!-- Some modal body, that will be restored on cache read -->
    </div>
    <?php $this->placeholder('modals')->captureEnd(); ?>
    <?php
    return 'Some finish message';
})
```


### CachedPartial

The same as standart partial helper, instead of `$key` parameter in invocation.

```php

echo $this->cachedPartial($someKey, $locationOrViewModal, $varsOrNothing);

```

## Renderer (in dev)

### Concept

Cache view model result to html with inner placeholder calls restore.

### View model rendering

In cases of cheap controller view model generation strategies you can easily provide you ViewModel with 
`cache_by_key` (Oprokidnev\CacheableRendering\View\Model::CACHE_PARAMETER_NAME) parameter. If render strategy find such models it will redirect view event to cache renderer and will try to inject a rendering result from cache. 

### Todos

 - Create a lazy loading view model, that can be transformed to every other ViewModel handled by other renderers.
