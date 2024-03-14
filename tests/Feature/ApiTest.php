<?php

namespace Tests\Feature;

use App\Services\GitHubService;
use Tests\TestCase;

/**
 * Checking the search for users and followers avatar, sending requests to the GitHub API.
 */
class ApiTest extends TestCase
{
    private GitHubService $service;

    private const GITHUB_USERNAME = 'taylorotwell';
    private const GITHUB_NAME = 'Taylor Otwell';

    public function setUp() : void
    {
        parent::setUp();

        $this->service = new GitHubService();
    }

    /**
     * Checking the search for user by username
     */
    public function test_get_by_username(): string
    {
        $response = $this->service->getUserByUsername(self::GITHUB_USERNAME);
        $this->assertTrue($response->successful());

        $user = $response->object();
        $this->assertIsObject($user);
        $this->assertObjectHasProperty('name', $user);
        $this->assertEquals(self::GITHUB_NAME, $user->name);
        $this->assertObjectHasProperty('followers', $user);
        $this->assertIsInt($user->followers);
        $this->assertObjectHasProperty('followers_url', $user);
        $this->assertNotEmpty($user->followers_url);

        return $user->followers_url;
    }

    /**
     * Checking receipt of followers avatar
     *
     * @depends test_get_by_username
     */
    public function test_get_followers_avatar(string $followersUrl): void
    {
        $response = $this->service->sendGetRequest($followersUrl);
        $this->assertTrue($response->successful());

        $url = $this->service->getNextUrlFromHeaders($response);
        $this->assertIsString($url);

        $avatars = data_get($response->object(), '*.avatar_url');
        $this->assertIsArray($avatars);
        $this->assertNotEmpty(current($avatars));
    }
}
