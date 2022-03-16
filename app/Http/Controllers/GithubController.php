<?php

namespace App\Http\Controllers;

use App\Models\Github\GithubUser;
use App\Models\Github\GithubUserStatistics;
use App\Services\Github\GithubUserService;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubController extends Controller
{

    const SORT_FIELDS = [
        'repositories' => 'repositories_count',
        'followers' => 'followers',
        'popularity' => 'statistics_count'
    ];

    /** @var GithubUserService */
    protected GithubUserService $githubUserService;

    public function __construct(
        GithubUserService $githubUserService
    )
    {
        $this->githubUserService = $githubUserService;
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->get('query') ?? '';

        $users = $this->githubUserService->getUsers($query);

        return response()->json($users);
    }

    public function userInfo(string $username): JsonResponse
    {
        $user = $this->githubUserService->getUser($username);

        return response()->json($user);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $sort = $request->get('sort', 'repositories');
        $direction = $request->get('direction', 'DESC');
        $sortField = self::SORT_FIELDS[$sort];

        $users = GithubUser::query()
            ->orderBy($sortField, $direction)
            ->paginate(3)
        ;

        return response()->json($users);
    }

    public function mostPopularUsers(Request $request): JsonResponse
    {
        $date = $request->date('date', 'Y-m-d') ?? now();

        $users_ids = GithubUserStatistics::query()
            ->whereDate('created_at', '=', $date)
            ->groupBy('github_user_id')
            ->pluck('github_user_id')
        ;

        $users = GithubUser::query()->whereIn('id', $users_ids)->get();

        return response()->json($users);
    }

    public function userRepositories(Request $request, string $username): JsonResponse
    {
        $query = $request->get('query') ?? '';

        $repositories = $this->githubUserService->getUserRepositories($username, $query);

        return response()->json($repositories);
    }

}
