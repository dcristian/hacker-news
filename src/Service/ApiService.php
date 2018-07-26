<?php

namespace Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    const ITEM_PATH = '/item/%d.json';
    const USER_PATH = '/user/%s.json';
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
     * @param int $id
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function getItem(int $id): ResponseInterface
    {
        $path = sprintf(ApiService::ITEM_PATH, $id);

        return $this->client->request('GET', $this->baseUrl . $path);
    }

    /**
     * @param string $id
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function getUser(string $id)
    {
        $path = sprintf(ApiService::USER_PATH, $id);

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