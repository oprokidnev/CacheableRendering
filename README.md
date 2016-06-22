# Cacheable placeholder module

Gives a set of utils for html cache with placeholder calls state restore.

## Installation

Initialize module in application.config.php and at first run you will get a new file in config/autoload named `cacheable-rendering.config.local.php`.
This is the config storage settings. By default I use filesystem cache. You can change this settings by the compatible with \Zend\Cache\StorageFactory::factory method configuration.

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
