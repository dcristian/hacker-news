<?php

namespace Controller;

use Silex\Application;

class NewsController
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function getAll(Application $app)
    {
        return $app['twig']->render('news.html.twig', array());
    }
}