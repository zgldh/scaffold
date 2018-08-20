<?php namespace Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Modules\Setting\Requests\CreateSettingRequest;
use Modules\Setting\Requests\UpdateSettingRequest;
use Modules\Setting\Repositories\SettingRepository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;
use Modules\Setting\Bundles\System;

class SettingController extends AppBaseController
{

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * List system settings bundle
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $bundle = $this->repository->getBundle(new System());
        return $this->sendResponse($bundle, 'System settings bundle');
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param    string $name
     * @param  UpdateSettingRequest $request
     *
     * @return  JsonResponse
     */
    public function update($name, UpdateSettingRequest $request, System $systemBundle)
    {
        $this->repository->updateByBundle($systemBundle, $name, $request->input('value'));
        $setting = $systemBundle->getSetting($name);

        return $this->sendResponse($setting, 'Setting updated successfully.');
    }
}
