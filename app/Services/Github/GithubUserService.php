<?php

namespace App\Services\Github;

use App\Models\Github\GithubUser;
use App\Models\Github\GithubUserRepository;
use Carbon\Carbon;
use Github\Client;
use Github\ResultPager;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GithubUserService
{

    /**
     * @param string $query
     * @return array
     */
    public function getUsers(string $query): array
    {
        $users = empty($query) ? GitHub::users()->all() : GitHub::search()->users($query)['items'];

        Log::info('search logging', [
            'query' => $query,
            'result' => $users
        ]);

        return $this->fillUsers($users);
    }

    /**
     * @param string $username
     * @return GithubUser
     */
    public function getUser(string $username): GithubUser
    {
        $user = GitHub::user()->show($username);

        $user = $this->fillUser($user);

        $this->updateRepositories($user);

        $this->updateStatistics($user);

        return $user->load('repositories');
    }

    private function processUser(array $user): array
    {
        $processedUser = Arr::only($user, GithubUser::AVAILABLE_FIELDS);
        if (isset($processedUser['created_at']) && !empty($processedUser['created_at'])) {
            $processedUser['created_at'] = Carbon::parse($processedUser['created_at']);
        }

        return $processedUser;
    }

    private function fillUser(array $user): GithubUser
    {
        $user = $this->processUser($user);

        return GithubUser::updateOrCreate(Arr::only($user, 'id'), $user);
    }

    private function fillUsers(array $users): array
    {
        $resultUsers = [];
        $processedUsers = array_map(fn($user) => $this->processUser($user), $users);

        DB::transaction(function() use ($processedUsers, &$resultUsers) {
            foreach($processedUsers as $user) {
                $resultUsers[] = $this->fillUser($user);
            }
        });

        return $resultUsers;
    }

    private function updateStatistics(GithubUser $githubUser): void
    {
        $githubUser->statistics()->create([
            'created_at' => now()
        ]);
    }

    private function updateRepositories(GithubUser $githubUser, array $repositories = null): void
    {
        $repositories = collect();

        $client = new Client();
        $paginator = new ResultPager($client, 100);

        $repositories->push(
            $paginator->fetch($client->api('user'), 'repositories', [$githubUser->login])
        );

        while($paginator->hasNext())
            $repositories->push($paginator->fetchNext());

        $repositories = $repositories->flatten(1)->toArray();

        $processedRepositories = array_map(fn($repo) => Arr::only($repo, GithubUserRepository::AVAILABLE_FIELDS), $repositories);

        DB::transaction(function() use ($githubUser, $processedRepositories) {
            foreach($processedRepositories as $processedRepository) {
                $githubUser->repositories()->updateOrCreate(Arr::only($processedRepository, 'id'), $processedRepository);
            }
        });
    }

    public function getUserRepositories(string $username, string $query = ''): array
    {
        $clearQuery = trim( preg_replace('/\s\s+/', ' ', $query) );
        $queryParts = explode(' ', $clearQuery);

        if (empty($username))
            return [];

        $githubUser = $this->getUser($username);

        if (empty($clearQuery) || empty($queryParts))
            return $githubUser->repositories->toArray();

        $repositories = $githubUser->repositories
            ->filter(function(GithubUserRepository $githubUserRepository) use ($queryParts) {
                foreach($queryParts as $queryPart) {
                    if (strpos($githubUserRepository->name, $queryPart) !== false)
                        return true;
                }

                return false;
            })
        ;

        return $repositories->toArray();
    }

}
