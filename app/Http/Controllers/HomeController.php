<?php

namespace App\Http\Controllers;

use App\Services\GitHubService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    protected GitHubService $service;

    /**
     * @return void
     */
    public function __construct(GitHubService $service)
    {
        $this->service = $service;
    }

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('home');
    }

    /**
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
