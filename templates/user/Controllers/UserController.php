<?php namespace $NAME$\User\Controllers;

use $NAME$\User\Repositories\UserRepository;
use $NAME$\User\Requests\CreateUserRequest;
use $NAME$\User\Requests\UpdateUserRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use zgldh\Scaffold\AppBaseController;

class UserController extends AppBaseController
{

    public function __construct(UserRepository $userRepo)
    {
        $this->repository = $userRepo;
        $this->middleware("auth");
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(IndexRequest $request)
    {
        $with = $request->getWith();
        $data = $this->repository->datatables(null, $with);
        return $data;
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $user = $this->repository->create($input);

        return $this->sendResponse($user, 'User saved successfully.');
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $this->repository->with($request->getWith());
        $user = $this->repository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse($user, '');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  int $id
     * @param UpdateUserRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->repository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user = $this->repository->update($request->all(), $id);

        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $user = $this->repository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $this->repository->delete($id);
        return $this->sendResponse($user, 'User deleted successfully.');
    }
}
