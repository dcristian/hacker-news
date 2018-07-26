<?php

namespace Controller\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class ItemProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     *
     * @return mixed|ControllerCollection
     */
    public function connect(Application $app)
    {
        $news = $app['controllers_factory'];

        $news->get('/', 'Controller\\ItemController::getTopStories')->bind('homepage');
        $news->get('/newest', 'Controller\\ItemController::getNewStories')->bind('newest');
        $news->get('/item/{id}', 'Controller\\ItemController::get')->bind('item');

        return $news;
    }
}