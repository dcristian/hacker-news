<?php

use Service\ApiService;
use GuzzleHttp\Client;
use Service\ItemService;
use Service\UserService;

$app['api.service'] = function ($app) {
    return new ApiService(new Client(), $app['hacker.news.api']);
};

$app['item.service'] = function ($app) {
    return new ItemService($app['api.service']);
};

$app['user.service'] = function ($app) {
    return new UserService($app['api.service']);
};