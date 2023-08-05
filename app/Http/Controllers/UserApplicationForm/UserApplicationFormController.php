<?php

namespace App\Http\Controllers\UserApplicationForm;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserApplicationForm\IndexUserApplicationFormRequest;
use App\Http\Requests\UserApplicationForm\StoreUserApplicationFormRequest;
use App\Http\Requests\UserApplicationForm\UpdateUserApplicationFormRequest;
use App\Http\Resources\UserApplicationForm\UserApplicationFormResource;
use App\Jobs\SendRequestEmail;
use App\Models\UserApplicationForm\UserApplicationForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApplicationFormController extends Controller
{
    public function index(IndexUserApplicationFormRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('employee')) {
            return response()->json([], 403);
        }

        $data = $request->validated();

        $userRequests = UserApplicationForm::indexFilter($data)
            ->paginate(50)
            ->map(fn(UserApplicationForm $userApplicationForm) => new UserApplicationFormResource($userApplicationForm));

        return response()->json($userRequests);
    }

    public function store(StoreUserApplicationFormRequest $request): JsonResponse
    {
        $data = $request->validated();

        $userApplicationForm = UserApplicationForm::create($data);

        if (!$userApplicationForm instanceof UserApplicationForm) {
            return response()->json([], 500);
        }

        return response()->json(new UserApplicationFormResource($userApplicationForm), 201);
    }

    public function show(Request $request, UserApplicationForm $userApplicationForm): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('employee')) {
            return response()->json([], 403);
        }

        return response()->json(new UserApplicationFormResource($userApplicationForm));
    }

    public function update(UpdateUserApplicationFormRequest $request, UserApplicationForm $userApplicationForm): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('employee')) {
            return response()->json([], 403);
        }

        $data = $request->validated();

        if ($data['send_email']) {
            SendRequestEmail::dispatch($userApplicationForm, $data['comment']);
        }

        $status = $userApplicationForm
            ->update([
                'status' => 'Resolved',
                'comment' => $data['comment'],
            ]);

        if (!$status) {
            return response()->json([], 500);
        }

        return response()->json(new UserApplicationFormResource($userApplicationForm));
    }

    public function destroy(): JsonResponse
    {
        return response()->json([], 419);
    }
}
