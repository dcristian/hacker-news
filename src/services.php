<?php

use Service\ApiService;
use GuzzleHttp\Client;
use Service\ItemService;

$app['api.service'] = function ($app) {
    return new ApiService(new Client(), $app['hacker.news.api']);
};

$app['item.service'] = function ($app) {
    return new ItemService($app['api.service']);
};