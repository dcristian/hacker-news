<?php

namespace Service;

use \GuzzleHttp\Exception\GuzzleException;

class ItemService
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

        $limit = 30;
        $results = [];

        for ($i = 0; $i<$limit; $i++) {
            $results[] = $this->get($json[$i]);
        }

        return $results;
    }

    /**
     * @param int $id
     * @param bool $withComments
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    public function get(int $id, $withComments = false)
    {
        $res = $this->apiService->getItem($id);

        if ($res->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $res->getStatusCode());
        }
        $json = json_decode($res->getBody(), true);

        $result = [
            'title' => $json['title'],
            'url' => $this->getValue($json, 'url'),
            'baseUrl' => $this->getBaseUrl($json),
            'author' => $json['by'],
            'score' => $json['score'],
            'totalComments' => $this->getValue($json, 'descendants'),
            'age' => $this->getAge($json),
            'id' => $json['id']
        ];

        if ($withComments) {
            $result['comments'] = $this->getComments($json);
        }

        return $result;
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

    /**
     * @param array $item
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    private function getComments(array $item)
    {
        $kids = isset($item['kids']) ? $item['kids'] : [];

        $comments = [];
        foreach ($kids as $id) {
            $comments[] = $this->getComment($id);
        }

        return $comments;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    private function getComment(int $id)
    {
        $res = $this->apiService->getItem($id);

        if ($res->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $res->getStatusCode());
        }
        $json = json_decode($res->getBody(), true);

        if (isset($json['deleted']) && $json['deleted']) {
            return [
                'text' => 'Deleted',
                'author' => 'Deleted',
                'age' => $this->getAge($json)
            ];
        }

        return [
            'text' => $json['text'],
            'author' => $json['by'],
            'age' => $this->getAge($json)
        ];
    }
}