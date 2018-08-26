<?php namespace Modules\Setting\Controllers;

use Illuminate\Http\Request;
use Modules\Setting\Requests\GetSettingRequest;
use Modules\Setting\Requests\UpdateSettingItemRequest;
use Modules\Setting\Repositories\SettingRepository;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;
use Modules\Setting\Bundles\System;
use Modules\Setting\Requests\UpdateSettingRequest;

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
     * @param GetSettingRequest $request
     * @return JsonResponse
     */
    public function index(GetSettingRequest $request)
    {
        $bundle = $this->repository->getBundle(new System(), $request->isDefault());
        return $this->sendResponse($bundle, 'System settings bundle');
    }

    /**
     * Reset system settings bundle
     *
     * @param Request $request
     * @param System $systemBundle
     * @return JsonResponse
     */
    public function reset(Request $request, System $systemBundle)
    {
        $systemBundle->persist();

        return $this->sendResponse($systemBundle, 'System setting has been reset successfully.');
    }

    /**
     * Update the specified system Setting in storage.
     *
     * @param    string $name
     * @param  UpdateSettingItemRequest $request
     *
     * @param System $systemBundle
     * @return  JsonResponse
     */
    public function update($name, UpdateSettingItemRequest $request, System $systemBundle)
    {
        $this->repository->updateByBundle($systemBundle, $name, $request->input('value'));
        $setting = $systemBundle->getSetting($name);

        return $this->sendResponse($setting, 'Setting item updated successfully.');
    }

    /**
     * Update all data of the system setting in storage.
     *
     * @param UpdateSettingRequest $request
     * @param System $bundle
     * @return  JsonResponse
     */
    public function updateAll(UpdateSettingRequest $request, System $bundle)
    {
        if ($request->isReset() === false) {
            $bundle = $this->repository->getBundle($bundle);
            $data = $request->json();
            foreach ($data as $name => $value) {
                if ($bundle->hasName($name) && $value != $bundle[$name]) {
                    $bundle[$name] = $value;
                }
            }
        }
        $bundle->persist();

        return $this->sendResponse($bundle, 'Setting updated successfully.');
    }
}
