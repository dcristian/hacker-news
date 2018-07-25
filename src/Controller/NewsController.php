<?php

namespace Controller;

use Silex\Application;
use \GuzzleHttp\Exception\GuzzleException;

class NewsController
{
    /**
     * @param Application $app
     *
     * @return mixed
     */
    public function getAll(Application $app)
    {
        try {
            $results = $app['news.service']->getAll();
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('news.html.twig', array(
            'results' => $results
        ));
    }
}