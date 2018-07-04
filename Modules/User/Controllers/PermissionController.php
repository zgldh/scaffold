<?php namespace Modules\User\Controllers;

use App\Scaffold\AppBaseController;
use Modules\User\Repositories\PermissionRepository;
use Modules\User\Requests\CreatePermissionRequest;
use Modules\User\Requests\SyncRolesRequest;
use Modules\User\Requests\UpdatePermissionRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends AppBaseController
{

    public function __construct(PermissionRepository $permissionRepo)
    {
        $this->repository = $permissionRepo;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * Display a listing of the Permission.
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
     * Store a newly created Permission in storage.
     *
     * @param CreatePermissionRequest $request
     * @return JsonResponse
     */
    public function store(CreatePermissionRequest $request)
    {
        $input = $request->all();

        $permission = $this->repository->create($input);
        $permission->load($request->getWith());

        return $this->sendResponse($permission, 'Permission saved successfully.');
    }

    /**
     * Display the specified Permission.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $permission = $this->repository->findWithoutFail($id);
        $permission->load($request->getWith());

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        return $this->sendResponse($permission, '');
    }

    /**
     * Update the specified Permission in storage.
     *
     * @param  int $id
     * @param UpdatePermissionRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdatePermissionRequest $request)
    {
        $permission = $this->repository->findWithoutFail($id);

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        $permission = $this->repository->update($request->all(), $id);
        $permission->load($request->getWith());

        return $this->sendResponse($permission, 'Permission updated successfully.');
    }

    /**
     * Remove the specified Permission from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $permission = $this->repository->findWithoutFail($id);

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        $this->repository->delete($id);

        return $this->sendResponse($permission, 'Permission deleted successfully.');
    }

    /**
     *
     * @param      $id
     * @param      \Modules\User\Requests\SyncRolesRequest $request
     * @return    JsonResponse
     */
    public function syncRoles($id, \Modules\User\Requests\SyncRolesRequest $request)
    {
        $roleIds = $request->input('roleIds');

        $this->repository->syncRoles($id, $roleIds);

        return $this->sendResponse(null, 'success');
    }
}
