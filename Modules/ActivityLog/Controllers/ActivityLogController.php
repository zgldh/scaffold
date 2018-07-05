<?php namespace Modules\ActivityLog\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use App\Scaffold\AppBaseController;
use Illuminate\Http\Request;
use Modules\ActivityLog\Repositories\ActivityLogRepository;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends AppBaseController
{

    public function __construct(ActivityLogRepository $activitylogRepo)
    {
        $this->repository = $activitylogRepo;
        $this->middleware("auth");
        $this->middleware("permission.auto");
    }

    /**
     * Display a listing of the ActivityLog.
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
     * Store a newly created ActivityLog in storage.
     *
     * @no-permission
     * @param Request $request
     * @return JsonResponse
     */
    public function store($request)
    {
    }

    /**
     * Display the specified ActivityLog.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $activityLog = $this->repository->findWithoutFail($id);
        $activityLog->load($request->getWith());

        if (empty($activityLog)) {
            return $this->sendError('ActivityLog not found');
        }

        return $this->sendResponse($activityLog, '');
    }

    /**
     * Update the specified ActivityLog in storage.
     *
     * @no-permission
     * @param  int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update($id, $request)
    {
    }

    /**
     * Remove the specified ActivityLog from storage.
     *
     * @no-permission
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
    }
}
