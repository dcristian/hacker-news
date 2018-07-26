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
     * @param int $limit
     * @param int $page
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    public function getAll(int $limit = 30, int $page = 1): array
    {
        $response = $this->apiService->getTopStories();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $response->getStatusCode());
        }

        $itemIds = json_decode($response->getBody(), true);

        /**
         * Manual pagination because the he API does not support limit and offset parameters
         */
        $end = $page * $limit;
        $start = $end - $limit;
        $last = count($itemIds) - 1;

        if ($start > $last) {
            throw new \Exception('Wrong page number!');
        }

        $end = min($end, $last);

        $results = [];
        for ($i = $start; $i<$end; $i++) {
            $results[] = $this->get($itemIds[$i]);
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
    public function get(int $id, $withComments = false): array
    {
        $response = $this->apiService->getItem($id);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $response->getStatusCode());
        }

        $item = json_decode($response->getBody(), true);

        $result = [
            'title' => $item['title'],
            'url' => $this->getValue($item, 'url'),
            'baseUrl' => $this->getBaseUrl($item),
            'author' => $item['by'],
            'score' => $item['score'],
            'totalComments' => $this->getValue($item, 'descendants'),
            'age' => $this->getAge($item),
            'id' => $item['id']
        ];

        if ($withComments) {
            $result['comments'] = $this->getComments($item);
        }

        return $result;
    }

    /**
     * @param array $item
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    private function getComments(array $item): array
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
    private function getComment(int $id): array
    {
        $response = $this->apiService->getItem($id);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $response->getStatusCode());
        }

        $comment = json_decode($response->getBody(), true);

        if (isset($comment['deleted']) && $comment['deleted']) {
            return [
                'text' => 'Deleted',
                'author' => 'Deleted',
                'age' => $this->getAge($comment)
            ];
        }

        return [
            'text' => $comment['text'],
            'author' => $comment['by'],
            'age' => $this->getAge($comment)
        ];
    }

    /**
     * @param array $item
     * @param $key
     *
     * @return mixed|null
     */
    private function getValue(array $item, $key)
    {
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

        $hostUrl = parse_url($item['url'], PHP_URL_HOST);

        return preg_replace('#^www\.(.+\.)#i', '$1', $hostUrl);
    }

    /**
     * @param array $item
     *
     * @return string
     */
    private function getAge(array $item): string
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