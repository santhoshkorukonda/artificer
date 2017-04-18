<?php

return [

    'stores' => [
    	# Artificer cache store configuration
        'artificer' => [
            'driver' => 'file',
            'path' => storage_path('artificer/cache'),
        ]

    ],

];
