<?php
    /**
     * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
     */
    $middleWare = $MODEL->getMiddleware();
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Controllers;

@if($MODEL->isActionLog())
use {{$MODULE_DIRECTORY_NAME}}\ActionLog\Models\ActionLog;
@endif
use {{$NAME_SPACE}}\Requests\Create{{$MODEL_NAME}}Request;
use {{$NAME_SPACE}}\Requests\Update{{$MODEL_NAME}}Request;
use {{$NAME_SPACE}}\Repositories\{{$MODEL_NAME}}Repository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use zgldh\Scaffold\AppBaseController;

class {{$MODEL_NAME}}Controller extends AppBaseController
{

    public function __construct({{$MODEL_NAME}}Repository $repository)
    {
        $this->repository = $repository;
@if($middleWare)
        $this->middleware("{{$middleWare}}");
@endif
    }

    /**
     * Display a listing of the {{$MODEL_NAME}}.
     *
     * @param IndexRequest $request
     * @return Response
     */
    public function index(IndexRequest $request)
    {
        $with = $request->getWith();
        $data = $this->repository->datatables(null, $with);
@if($MODEL->isActionLog())
        ActionLog::log(ActionLog::TYPE_SEARCH, "{{$NAME_SPACE}}\{{$MODEL_NAME}}");
@endif
        return $data;
    }

    /**
     * Store a newly created {{$MODEL_NAME}} in storage.
     *
     * @param Create{{$MODEL_NAME}}Request $request
     * @return JsonResponse
     */
    public function store(Create{{$MODEL_NAME}}Request $request)
    {
        $input = $request->all();

        $item = $this->repository->create($input);
        $item->load($request->getWith());
@if($MODEL->isActionLog())
        ActionLog::log(ActionLog::TYPE_CREATE, "{{$NAME_SPACE}}\{{$MODEL_NAME}}");
@endif
        return $this->sendResponse($item, '{{$MODEL_NAME}} saved successfully.');
    }

    /**
     * Display the specified {{$MODEL_NAME}}.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $item = $this->repository->findWithoutFail($id);
        $item->load($request->getWith());

        if (empty($item)) {
            return $this->sendError('{{$MODEL_NAME}} not found');
        }
@if($MODEL->isActionLog())
        ActionLog::log(ActionLog::TYPE_SHOW, "{{$NAME_SPACE}}\{{$MODEL_NAME}}");
@endif
        return $this->sendResponse($item, '');
    }

    /**
     * Update the specified {{$MODEL_NAME}} in storage.
     *
     * @param  int $id
     * @param Update{{$MODEL_NAME}}Request $request
     *
     * @return JsonResponse
     */
    public function update($id, Update{{$MODEL_NAME}}Request $request)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('{{$MODEL_NAME}} not found');
        }

        $item = $this->repository->update($request->all(), $id);
        $item->load($request->getWith());
@if($MODEL->isActionLog())
        ActionLog::log(ActionLog::TYPE_UPDATE, "{{$NAME_SPACE}}\{{$MODEL_NAME}}");
@endif
        return $this->sendResponse($item, '{{$MODEL_NAME}} updated successfully.');
    }

    /**
     * Remove the specified {{$MODEL_NAME}} from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('{{$MODEL_NAME}} not found');
        }

        $this->repository->delete($id);
@if($MODEL->isActionLog())
        ActionLog::log(ActionLog::TYPE_DELETE, "{{$NAME_SPACE}}\{{$MODEL_NAME}}");
@endif
        return $this->sendResponse($item, '{{$MODEL_NAME}} deleted successfully.');
    }
}
