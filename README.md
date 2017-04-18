# Artificer
Build dynamic HTML forms with Artificer using JSON.

## Introduction
Artificer provides a simple API to generate HTML forms by storing its schema in JSON. It caches the generated HTML form for later requests which improves speed in generation and serving forms.

### Prerequisites
1. requires ``php >= 7.1``
2. requires ``laravel/framework >= 5.4``
3. requires ``laravelcollective/html >= 5.4``

## Installation
To install this package through composer, run following command in terminal:
``composer require santhoshkorukonda/artificer``

## Configuration
We need to setup little configuration before we start using it.

Add our new provider **Artificer ServiceProvider** to the providers array of ``config/app.php``:

```php
<?php

return [
    'providers' => [
        SantoshKorukonda\Artificer\ArtificerServiceProvider::class,
    ],
];
```

Next, add an alias to aliases array of ``config/app.php``:

```php
<?php

return [
    'aliases' => [
        SantoshKorukonda\Artificer\ArtificerFacade::class,
    ],
];
```

Next, create a cache store for **Artificer** by adding config to stores array in ``config/cache.php``:

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

Next, create a filesystem disk for **Artificer** by adding config to disks array in ``config/filesystems.php``:

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

## Generating Forms

```php
<?php

public function create()
{
    // decode json and send it as an object into build method
    $schema = json_decode({});
    $data = Artificer::build($schema);
    return view("welcome")->with($data);
}
```
Its that simple to generate a form.
