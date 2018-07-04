<?php namespace Modules\User\Controllers;

use App\Scaffold\AppBaseController;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Requests\CreateRoleRequest;
use Modules\User\Requests\UpdateRoleRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends AppBaseController
{

    public function __construct(RoleRepository $roleRepo)
    {
        $this->repository = $roleRepo;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * Display a listing of the Role.
     *
     * @param IndexRequest $request
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
        $role->load($request->getWith());

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
        $role = $this->repository->findWithoutFail($id);
        $role->load($request->getWith());

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
        $role->load($request->getWith());

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

    /**
     *
     * @param      \Modules\User\Requests\PostCopyRoleRequest $request
     * @return    JsonResponse
     */
    public function copy(\Modules\User\Requests\PostCopyRoleRequest $request)
    {
        $oldRole = $this->repository->findWithoutFail($request->input('id'));
        $newRoleName = $request->input('name');

        $result = $this->repository->copy($oldRole, $newRoleName);

        return $this->sendResponse($result, 'success');
    }
}
