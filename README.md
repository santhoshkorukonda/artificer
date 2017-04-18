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

Add our new provider **ArtificerServiceProvider** to the providers array of ``config/app.php``:

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

Next, create a cache store for ``Artificer`` by adding config to stores array in ``config/cache.php``:

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

Next, create a filesystem disk for ``Artificer`` by adding config to disks array in ``config/filesystems.php``:

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
A sample form generation code with a controller and sample json text.

```php
<?php

namespace App\Http\Controllers;

use Artificer;

class FormController extends Controller
{
    /**
     * Build a sample form from the json.
     *
     * @return html
     */
    public function create()
    {
        // Here json is hardcoded as string to explain you how it works,
        // it can even fetched form a database or a remote http call etc.
        // So whatever it might just fetch the string, decode it and send
        // the schema to build the form.
        $schema = json_decode($this->getJson());
        $data = Artificer::build($schema);
        return view("welcome")->with($data);
    }

    /**
     * Define a sample json schema for form generation.
     *
     * @return string
     */
    protected function getJson()
    {
        return '{}';
    }
}

```
Its that simple to generate a form.

## Understanding form JSON schema
Checkout following documentation on how to build the json schema which is recognized by the **Artificer**.

### Form schema
Form schema attributes are same as form options of [laravelcollective](https://laravelcollective.com/docs/5.3/html#opening-a-form).
```js
{
    // define attributes for the form with the key "attributes" in json schema.
    "attributes": {
        "route": "route.name",
        "method": "POST",
        "files": true,
        "id": "Enquiry",
        "class": "form"
    },
    "components": {
        ...
    }
}
```


