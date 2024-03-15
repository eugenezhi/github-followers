<?php

namespace App\Http\Controllers;

use App\Services\GitHubService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/*
 * Controller to retrieve data from GitHub API
 */
class HomeController extends Controller
{
    public function __construct(
        protected GitHubService $service
    ) {}

    /**
     * Render home page with search form
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('home');
    }

    /**
     * Retrieve user data by GitHub username
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function searchByUsername(Request $request): JsonResponse
    {
        $this->validate($request, ['username' => ['required', 'string']]);

        $response = $this->service->getUserByUsername($request->get('username'));
        if (!$response->successful()) {
            return response()->json([], 404);
        }

        $user = $response->object();
        $data = [
            'name' => optional($user)->name,
            'followers' => optional($user)->followers,
            'followers_url' => optional($user)->followers_url
        ];
        return response()->json($data);
    }

    /**
     * Get subscribers' avatar data from the url returned by GitHub
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getFollowers(Request $request): JsonResponse
    {
        $this->validate($request, ['url' => ['required', 'string']]);

        $response = $this->service->sendGetRequest($request->get('url'));
        if (!$response->successful()) {
            return response()->json([], 404);
        }

        $url = $this->service->getNextUrlFromHeaders($response);
        $data = [
            'avatar' => data_get($response->object(), '*.avatar_url'),
            'load_more_url' => $url
        ];
        return response()->json($data);
    }
}
