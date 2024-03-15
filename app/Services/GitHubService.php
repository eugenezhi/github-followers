<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Service for working with GitHub API
 * Docs: https://docs.github.com/en/rest?apiVersion=2022-11-28
 *
 * Class GitHubService
 * @package App\Services
 */
class GitHubService
{
    private string $api_url;
    private array $headers;

    /*
     * Setting the parameters for making API requests
     * Parameters must be specified in .env
     * Docs: https://docs.github.com/en/rest/authentication/authenticating-to-the-rest-api?apiVersion=2022-11-28
     */
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
     * The method for executing requests to the API (Contains authentication headers)
     *
     * @param string $url
     * @param $query
     * @return PromiseInterface|Response
     */
    public function sendGetRequest(string $url, $query = null): PromiseInterface|Response
    {
        return Http::withHeaders($this->headers)->get($url, $query);
    }

    /**
     * Get user data by username
     * Docs: https://docs.github.com/en/rest/users/users?apiVersion=2022-11-28#get-a-user
     *
     * @param string $username
     * @return PromiseInterface|Response
     */
    public function getUserByUsername(string $username): PromiseInterface|Response
    {
        return $this->sendGetRequest($this->api_url . 'users/' . $username);
    }

    /**
     * Get user followers by username
     * Docs: https://docs.github.com/en/rest/users/followers?apiVersion=2022-11-28#list-followers-of-a-user
     *
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
     * The method retrieves the url to the next page of followers from the response headers
     * Used for pagination
     * Docs: https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api?apiVersion=2022-11-28
     *
     * @param PromiseInterface|Response $response
     * @return string
     */
    public function getNextUrlFromHeaders(PromiseInterface|Response $response): string
    {
        $url = '';
        $linkHeader = Arr::get($response->headers(), 'Link.0', '');
        $pagesRemaining = $linkHeader && str_contains($linkHeader, 'rel="next"');

        // If the next page exists > retrieve the URL from the 'link' header string
        if ($pagesRemaining) {
            $nextPattern = "/(?<=<)([\S]*)(?=>; rel=\"Next\")/i";
            preg_match($nextPattern, $linkHeader, $matches, PREG_OFFSET_CAPTURE);
            $url = current($matches[0]);
        }
        return $url;
    }
}
