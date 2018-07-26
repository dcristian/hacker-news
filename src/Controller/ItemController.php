<?php

namespace Controller;

use Silex\Application;
use \GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class ItemController
{
    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getAll(Application $app, Request $request)
    {
        $limit = 30;
        $page = $request->query->get('page', 1);

        try {
            $results = $app['item.service']->getAll($limit, $page);
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('item-list.html.twig', array(
            'itemList' => $results,
            'page' => $page,
            'startIndex' => $limit * ($page-1)
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

        return $app['twig']->render('item-with-comments.html.twig', array(
            'item' => $result
        ));
    }
}