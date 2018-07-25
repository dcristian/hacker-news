<?php

namespace Controller;

use Silex\Application;
use \GuzzleHttp\Exception\GuzzleException;

class ItemController
{
    /**
     * @param Application $app
     *
     * @return mixed
     */
    public function getAll(Application $app)
    {
        try {
            $results = $app['item.service']->getAll();
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('item-list.html.twig', array(
            'results' => $results
        ));
    }

    /**
     * @param Application $app
     * @param $id
     *
     * @return mixed
     */
    public function get(Application $app, $id)
    {
        try {
            $result = $app['item.service']->get($id, true);
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('item.html.twig', array(
            'item' => $result
        ));
    }
}