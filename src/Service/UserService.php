<?php

namespace Service;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
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
     * @param string $id
     *
     * @return array
     *
     * @throws GuzzleException|\Exception
     */
    public function get(string $id): array
    {
        $response = $this->apiService->getUser($id);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('The API request failed with status code ' . $response->getStatusCode());
        }

        $user = json_decode($response->getBody(), true);
        if (!$user) {
            throw new NotFoundHttpException('User not found!');
        }

        return [
            'id' => $user['id'],
            'about' => $user['about'],
            'karma' => $user['karma'],
            'created' => $user['created'],
        ];
    }
}