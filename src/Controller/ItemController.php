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
    public function getTopStories(Application $app, Request $request)
    {
        $limit = 30;
        $page = $request->query->get('page', 1);

        try {
            $result = $app['item.service']->getTopStories($limit, $page);
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        $nextPageNumber = $page + 1;

        $nextPage = null;
        if ($nextPageNumber <= $result['totalPages']) {
            $nextPage = $app['url_generator']->generate('homepage', ['page' => ($nextPageNumber)]);
        }

        return $app['twig']->render('item-list.html.twig', [
            'itemList' => $result['itemList'],
            'itemStartIndex' => $limit * ($page-1),
            'nextPage' => $nextPage
        ]);
    }

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getNewStories(Application $app, Request $request)
    {
        $limit = 30;
        $page = $request->query->get('page', 1);

        try {
            $result = $app['item.service']->getNewStories($limit, $page);
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        $nextPageNumber = $page + 1;

        $nextPage = null;
        if ($nextPageNumber <= $result['totalPages']) {
            $nextPage = $app['url_generator']->generate('newest', ['page' => ($nextPageNumber)]);
        }

        return $app['twig']->render('item-list.html.twig', [
            'itemList' => $result['itemList'],
            'itemStartIndex' => $limit * ($page-1),
            'nextPage' => $nextPage
        ]);
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

        return $app['twig']->render('item-with-comments.html.twig', [
            'item' => $result
        ]);
    }
}