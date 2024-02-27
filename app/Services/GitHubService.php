<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class GitHubService
 * @package App\Services
 */
class GitHubService
{
    private string $api_url;
    private array $headers;

    public function __construct()
    {
        $this->api_url = env('GITHUB_API_URL');
        $this->headers = [
            'X-GitHub-Api-Version' => env('GITHUB_API_VERSION'),
            'Authorization' => 'Bearer ' . env('GITHUB_ACCESS_TOKEN'),
            'Accept' => 'application/vnd.github+json'
        ];
    }

    /**
     * @param string $url
     * @param $query
     * @return PromiseInterface|Response
     */
    public function sendGetRequest(string $url, $query = null): PromiseInterface|Response
    {
        return Http::withHeaders($this->headers)->get($url, $query);
    }

    /**
     * @param string $username
     * @return PromiseInterface|Response
     */
    public function getUserByUsername(string $username): PromiseInterface|Response
    {
        return $this->sendGetRequest($this->api_url . 'users/' . $username);
    }

    /**
     * @param string $username
     * @param int $page
     * @return PromiseInterface|Response
     */
    public function getFollowers(string $username, int $page = 1): PromiseInterface|Response
    {
        return $this->sendGetRequest($this->api_url . "users/{$username}/followers", [
            'page' => $page
        ]);
    }

    /**
     * @param PromiseInterface|Response $response
     * @return string
     */
    public function getNextUrlFromHeaders(PromiseInterface|Response $response): string
    {
        $url = '';
        $linkHeader = Arr::get($response->headers(), 'Link.0', '');
        $pagesRemaining = $linkHeader && str_contains($linkHeader, 'rel="next"');

        if ($pagesRemaining) {
            $nextPattern = "/(?<=<)([\S]*)(?=>; rel=\"Next\")/i";
            preg_match($nextPattern, $linkHeader, $matches, PREG_OFFSET_CAPTURE);
            $url = current($matches[0]);
        }
        return $url;
    }
}
