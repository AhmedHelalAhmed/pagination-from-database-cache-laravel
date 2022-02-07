<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
//        return $this->getUsersPaginated();
        return $this->getUsersCustomPagination();
    }

    private function getUsersCustomPagination()
    {

        $totalCount = User::getCountOfSchema();
        $perPage = request('perPage', 10);
        $page = request('page', 0);
        $skip = $page + $perPage;
        $take = $skip + $perPage;
        $prevUrl = route('users.index', ['page' => $page > 0 ? $page - 1 : $page]);
        $nextUrl = route('users.index', ['page' => $page + 1]);
        $users = User::query()->skip($skip)->take($take)->get();

        return response()->json([
            'data' => UserResource::make($users),
            'links' => [
                "first" => route('users.index', ['page' => 1]),
                "last" => route('users.index', ['page' => intval($totalCount / $perPage)]),
                "prev" => $prevUrl,
                "next" => $nextUrl
            ],
            'meta' => [
                "current_page" => $page,
                "from" => 1,
                "last_page" => $totalCount / $perPage,
                "path" => route('users.index'),
                "per_page" => $perPage,
                "to" => $perPage,
                "total" => $totalCount
            ]
        ]);

    }

    private function getUsersPaginated()
    {
        return UserResource::collection(User::paginate(request('perPage', 10)));
    }
}
