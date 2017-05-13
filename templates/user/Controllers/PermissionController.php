<?php namespace $NAME$\User\Controllers;

use $NAME$\User\Repositories\PermissionRepository;
use $NAME$\User\Requests\CreatePermissionRequest;
use $NAME$\User\Requests\UpdatePermissionRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use zgldh\Scaffold\AppBaseController;

class PermissionController extends AppBaseController
{

    public function __construct(PermissionRepository $permissionRepo)
    {
        $this->repository = $permissionRepo;
        $this->middleware("auth");
    }

    /**
     * Display a listing of the Permission.
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
     * Store a newly created Permission in storage.
     *
     * @param CreatePermissionRequest $request
     * @return JsonResponse
     */
    public function store(CreatePermissionRequest $request)
    {
        $input = $request->all();

        $permission = $this->repository->create($input);

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
        $this->repository->with($request->getWith());
        $permission = $this->repository->findWithoutFail($id);

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
}
