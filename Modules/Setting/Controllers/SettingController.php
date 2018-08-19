<?php namespace Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Modules\Setting\Requests\CreateSettingRequest;
use Modules\Setting\Requests\UpdateSettingRequest;
use Modules\Setting\Repositories\SettingRepository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;
use Modules\Setting\Sets\System;

class SettingController extends AppBaseController
{

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * List system settings set
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $set = $this->repository->getSettingSet(new System());
        return $this->sendResponse($set, 'System settings set');
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param    string $name
     * @param  UpdateSettingRequest $request
     *
     * @return  JsonResponse
     */
    public function update($name, UpdateSettingRequest $request)
    {
        $set = $this->repository->getSettingSet(new System());
        $set->update($name, $request->input('value'));
        $setting = $set->getSetting($name);

        return $this->sendResponse($setting, 'Setting updated successfully.');
    }
}
