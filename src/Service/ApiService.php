<?php

namespace Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    const ITEM_PATH = '/item/%d.json';
    const TOP_STORIES_PATH = '/topstories.json';
    const NEW_STORIES_PATH = '/newstories.json';

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
     * @param int $itemId
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function getItem(int $itemId): ResponseInterface
    {
        $path = sprintf(ApiService::ITEM_PATH, $itemId);

        return $this->client->request('GET', $this->baseUrl . $path);
    }

    /**
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function getTopStories(): ResponseInterface
    {
        return $this->client->request('GET', $this->baseUrl . ApiService::TOP_STORIES_PATH);
    }

    /**
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function getNewStories()
    {
        return $this->client->request('GET', $this->baseUrl . ApiService::NEW_STORIES_PATH);
    }
}