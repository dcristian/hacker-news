<?php

namespace Controller;

use GuzzleHttp\Exception\GuzzleException;
use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController
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
            $result = $app['user.service']->get($id);
        } catch(GuzzleException $exception) {
            return $app['twig']->render('errors\default.html.twig');
        } catch (NotFoundHttpException $exception) {
            return $app['twig']->render('errors\custom.html.twig', [
                'message' => $exception->getMessage()
            ]);
        } catch (\Exception $exception) {
            return $app['twig']->render('errors\default.html.twig');
        }

        return $app['twig']->render('user.html.twig', [
            'user' => $result
        ]);
    }
}