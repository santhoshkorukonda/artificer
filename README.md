# Artificer
Build dynamic HTML forms with Artificer using JSON.

### Introduction
Artificer provides a simple API to generate HTML forms by storing its schema in JSON. It caches the generated HTML form for later requests which improves speed in generation and serving forms.

#### Prerequisites
1. requires ``php >= 7.1``
2. requires ``laravel/framework >= 5.4``
3. requires ``laravelcollective/html >= 5.4``

#### Installation
``composer require santhoshkorukonda/artificer``

### Configuration
We need to setup little configuration before we start using it.

Add **Artificer ServiceProvider** to ``config/app.php``

```php
<?php

return [
    'providers' => [
        SantoshKorukonda\Artificer\ArtificerServiceProvider::class,
    ],
];
```

Add **Artificer Facade** to ``config/app.php``

```php
<?php

return [
    'aliases' => [
        SantoshKorukonda\Artificer\ArtificerFacade::class,
    ],
];
```

Create **Artificer cache store** in ``config/cache.php``

```php
<?php

return [
    'stores' => [
        'artificer' => [
            'driver' => 'file',
            'path' => storage_path('artificer/cache'),
        ]
    ],
];
```

Create **Artificer filesystem disk** in ``config/filesystems.php``

```php
<?php

return [
    'disks' => [
        'artificer' => [
            'driver' => 'local',
            'root' => storage_path('artificer/views'),
        ],
    ],
];
```
