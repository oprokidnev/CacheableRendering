# Cacheable placeholder module

Provides a set of utils for html caching including placeholder view helper calls interception and restore.

## Installation

```
composer require oprokidnev/cacheable-rendering:^2.0
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
echo $this->cachedCallback($key, function ($cacheTags) use ($serviceFromController) { ?>
    <?php foreach($veryBigIteratorWithLazyLoading->getItems() as $item): ?>
        <div class="item"><?= $item->getName()?></div>
    <?php endforeach; ?>
    <?php $this->placeholder('modals')->captureStart(); // placeholder call happens here ?>
    <div class="modal">
        <!-- Some modal body, that will be restored on cache read -->
    </div>
    <?php $this->placeholder('modals')->captureEnd(); ?>
    <?php
    $cacheTags[] = 'database_cache';
    return 'Some finish message';
}); ?>

```


### CachedPartial

The same as standart partial helper, instead of `$key` parameter in invocation.

```php

echo $this->cachedPartial($someKey, $locationOrViewModal, $varsOrNothing);

```
