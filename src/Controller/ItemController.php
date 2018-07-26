<?php

namespace Controller;

use Silex\Application;
use \GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemController
{
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
        } catch (NotFoundHttpException $exception) {
            return $app['twig']->render('errors\custom.html.twig', [
                'message' => $exception->getMessage()
            ]);
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('item-with-comments.html.twig', [
            'item' => $result
        ]);
    }
    
    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getTopStories(Application $app, Request $request)
    {
        return $this->getItemList($app, $request, 'top');
    }

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getNewStories(Application $app, Request $request)
    {
        return $this->getItemList($app, $request, 'new');
    }

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getShowStories(Application $app, Request $request)
    {
        return $this->getItemList($app, $request, 'show');
    }

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return mixed
     */
    public function getAskStories(Application $app, Request $request)
    {
        return $this->getItemList($app, $request, 'ask');
    }


    /**
     * @param Application $app
     * @param Request $request
     * @param string $type
     *
     * @return mixed
     */
    private function getItemList(Application $app, Request $request, string $type)
    {
        $limit = 30;
        $page = $request->query->get('page', 1);
        $result = [];
        $pathName = null;

        $itemService = $app['item.service'];

        try {
            switch ($type) {
                case 'top':
                    $result = $itemService->getTopStories($limit, $page);
                    $pathName = 'homepage';
                    break;
                case 'new':
                    $result = $itemService->getNewStories($limit, $page);
                    $pathName = 'newest';
                    break;
                case 'show':
                    $result = $itemService->getShowStories($limit, $page);
                    $pathName = 'show';
                    break;
                case 'ask':
                    $result = $itemService->getAskStories($limit, $page);
                    $pathName = 'ask';
            }
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        $nextPageNumber = $page + 1;

        $nextPage = null;
        if ($nextPageNumber <= $result['totalPages']) {
            $nextPage = $app['url_generator']->generate($pathName, ['page' => ($nextPageNumber)]);
        }

        return $app['twig']->render('item-list.html.twig', [
            'itemList' => $result['itemList'],
            'itemStartIndex' => $limit * ($page-1),
            'nextPage' => $nextPage
        ]);
    }
}