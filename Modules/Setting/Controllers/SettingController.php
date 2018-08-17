<?php namespace Modules\Setting\Controllers;

use Modules\Setting\Requests\CreateSettingRequest;
use Modules\Setting\Requests\UpdateSettingRequest;
use Modules\Setting\Repositories\SettingRepository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;

class SettingController extends AppBaseController
{

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * Display a listing of the Setting.
     *
     * @param  IndexRequest $request
     * @return  Response
     */
    public function index(IndexRequest $request)
    {
        $data = $this->repository->datatables(null, $request->getWith())
            ->search($request->getColumns(), null)
            ->result($request->getExportFileName());

        return $data;
    }

    /**
     * Store a newly created Setting in storage.
     *
     * @param  CreateSettingRequest $request
     * @return  JsonResponse
     */
    public function store(CreateSettingRequest $request)
    {
        $input = $request->all();

        $item = $this->repository->create($input);
        $item->load($request->getWith());

        return $this->sendResponse($item, 'Setting saved successfully.');
    }

    /**
     * Display the specified Setting.
     *
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $item = $this->repository->findWithoutFail($id);
        if (empty($item)) {
            return $this->sendError('Setting not found');
        }
        $item->load($request->getWith());

        return $this->sendResponse($item, '');
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param    int $id
     * @param  UpdateSettingRequest $request
     *
     * @return  JsonResponse
     */
    public function update($id, UpdateSettingRequest $request)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('Setting not found');
        }

        $item = $this->repository->update($request->all(), $id);
        $item->load($request->getWith());

        return $this->sendResponse($item, 'Setting updated successfully.');
    }

    /**
     * Remove the specified Setting from storage.
     *
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function destroy($id)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('Setting not found');
        }

        $this->repository->delete($id);

        return $this->sendResponse($item, 'Setting deleted successfully.');
    }
}
