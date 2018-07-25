<?php

use Service\ApiService;
use GuzzleHttp\Client;
use Service\NewsService;

$app['api.service'] = function ($app) {
    return new ApiService(new Client(), $app['hacker.news.api']);
};

$app['news.service'] = function ($app) {
    return new NewsService($app['api.service']);
};