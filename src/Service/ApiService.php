<?php

namespace Service;

use GuzzleHttp\Client;

class ApiService
{
    const TOP_STORIES_PATH = '/topstories.json';
    const ITEM_PATH = '/item/%d.json';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param Client $client
     * @param string $baseUrl
     */
    public function __construct(Client $client, string $baseUrl)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTopStories()
    {
        return $this->client->request('GET', $this->baseUrl . ApiService::TOP_STORIES_PATH);
    }

    /**
     * @param int $itemId
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getItem(int $itemId)
    {
        $path = sprintf(ApiService::ITEM_PATH, $itemId);

        return $this->client->request('GET', $this->baseUrl . $path);
    }
}