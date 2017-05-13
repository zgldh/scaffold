<?php namespace $NAME$\User\Controllers;

use $NAME$\User\Repositories\RoleRepository;
use $NAME$\User\Requests\CreateRoleRequest;
use $NAME$\User\Requests\UpdateRoleRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use zgldh\Scaffold\AppBaseController;

class RoleController extends AppBaseController
{

    public function __construct(RoleRepository $roleRepo)
    {
        $this->repository = $roleRepo;
        $this->middleware("auth");
    }

    /**
     * Display a listing of the Role.
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
     * Store a newly created Role in storage.
     *
     * @param CreateRoleRequest $request
     * @return JsonResponse
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();

        $role = $this->repository->create($input);

        return $this->sendResponse($role, 'Role saved successfully.');
    }

    /**
     * Display the specified Role.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $this->repository->with($request->getWith());
        $role = $this->repository->findWithoutFail($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        return $this->sendResponse($role, '');
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  int $id
     * @param UpdateRoleRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $role = $this->repository->findWithoutFail($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role = $this->repository->update($request->all(), $id);

        return $this->sendResponse($role, 'Role updated successfully.');
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $role = $this->repository->findWithoutFail($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $this->repository->delete($id);
        return $this->sendResponse($role, 'Role deleted successfully.');
    }
}
