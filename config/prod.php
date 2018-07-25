<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');
$app['hacker.news.api'] = 'https://hacker-news.firebaseio.com/v0';
