<?php

return [
   'host' => [
        env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200'),
    ],
    'port' => env('ELASTICSEARCH_PORT', 9200),
    'index' => env('ELASTICSEARCH_INDEX', 'products'),
    'timeout' => env('ELASTICSEARCH_TIMEOUT', 60),
    'retries' => env('ELASTICSEARCH_RETRIES', 3),

];