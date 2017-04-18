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
        $schema = json_decode($this->getJson())[0];
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
        return '[{"attributes":{"route":"route.name","method":"POST","files":true,"id":"Enquiry","class":"form"},"components":[{"name":"bsText","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Email","text":"Email*","attributes":[]},"input":{"name":"Email","value":null,"attributes":[]}}},{"name":"bsFile","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":{"name":"Image","value":null,"attributes":[]}}},{"name":"bsSelectWithDb","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Units","text":"Units*","attributes":[]},"input":{"name":"Units","value":null,"attributes":[],"database":{"table":"City","columns":["Id","Name"],"where":{"StateId":1403},"uid":"Cities"}}}},{"name":"bsCheckbox","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsRadio","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsButton","options":{"row":{"start":true,"end":false},"wrapper":{"start":true,"end":false,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Submit","attributes":{"name":"Submit","type":"submit","class":"btn btn-primary"}}}},{"name":"bsButton","options":{"row":{"start":false,"end":true},"wrapper":{"start":false,"end":true,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Reset","attributes":{"name":"Reset","type":"reset","class":"btn btn-default"}}}}]}]';
    }
}

```
Its that simple to generate a form.
