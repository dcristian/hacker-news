<?php

namespace Controller;

use Silex\Application;

class NewsController
{
    const BASE_URL = 'https://hacker-news.firebaseio.com/v0/';

    /**
     * @param Application $app
     *
     * @return mixed
     */
    public function getAll(Application $app)
    {
        $content = file_get_contents(NewsController::BASE_URL . 'topstories.json');
        $json = json_decode($content, true);

        // TODO: use this to limit the number of results and remove it from the loop
        $limit = 10;
        $results = [];

        for ($i = 0; $i<$limit; $i++) {
           $contentItem = file_get_contents(NewsController::BASE_URL . '/item/' . $json[$i] . '.json');
           $jsonItem = json_decode($contentItem, true);

           $results[] = [
               'title' => $jsonItem['title'],
               'url' => $this->getValue($jsonItem, 'url'),
               'baseUrl' => $this->getBaseUrl($jsonItem),
               'author' => $jsonItem['by'],
               'score' => $jsonItem['score'],
               'comments' => $this->getValue($jsonItem, 'descendants'),
               'age' => $this->getAge($jsonItem)
           ];
        }

        return $app['twig']->render('news.html.twig', array(
            'results' => $results
        ));
    }

    /**
     * @param array $item
     * @param $key
     *
     * @return mixed|null
     */
    private function getValue(array $item, $key) {
        return isset($item[$key]) ? $item[$key] : null;
    }

    /**
     * @param array $item
     *
     * @return null|string
     */
    private function getBaseUrl(array $item)
    {
        if (!isset($item['url'])) {
            return null;
        }

        $parse = parse_url($item['url']);

        return preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']);
    }

    /**
     * @param array $item
     *
     * @return string
     */
    private function getAge(array $item)
    {
        $createdAt = new \DateTime('@' . $item['time']);
        $now = new \DateTime();
        $interval = $now->diff($createdAt);

        $formats = [
            '%y' => 'years',
            '%m' => 'months',
            '%d' => 'days',
            '%h' => 'hours',
            '%i' => 'minutes',
        ];

        foreach ($formats as $format => $value) {
            if ((int)$interval->format($format) > 0) {
                return $interval->format($format) . ' ' . $value;
            }
        }

        return '0 minutes';
    }
}