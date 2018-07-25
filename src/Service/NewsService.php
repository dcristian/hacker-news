<?php

namespace Service;

use \GuzzleHttp\Exception\GuzzleException;

class NewsService
{
    /**
     * @var ApiService
     */
    private $apiService;

    /**
     * @param ApiService $apiService
     */
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    public function getAll()
    {
        $res = $this->apiService->getTopStories();

        if ($res->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $res->getStatusCode());
        }
        $json = json_decode($res->getBody(), true);

        // TODO: use this to limit the number of results and remove it from the loop
        $limit = 30;
        $results = [];

        for ($i = 0; $i<$limit; $i++) {
            $res = $this->apiService->getItem($json[$i]);

            if ($res->getStatusCode() !== 200) {
                throw new \Exception('The API request failed with status code ' . $res->getStatusCode());
            }
            $jsonItem = json_decode($res->getBody(), true);

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

        return $results;
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