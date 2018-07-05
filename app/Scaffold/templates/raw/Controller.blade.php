<?php
    /**
     * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \App\Scaffold\Installer\Model\Field
     */
    $middleWare = $MODEL->getMiddleware();
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Controllers;

use {{$NAME_SPACE}}\Requests\Create{{$MODEL_NAME}}Request;
use {{$NAME_SPACE}}\Requests\Update{{$MODEL_NAME}}Request;
use {{$NAME_SPACE}}\Repositories\{{$MODEL_NAME}}Repository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;

class {{$MODEL_NAME}}Controller extends AppBaseController
{

    public function __construct({{$MODEL_NAME}}Repository $repository)
    {
        $this->repository = $repository;
@if($middleWare)
        $this->middleware("{{$middleWare}}");
@endif
        $this->middleware("permission.auto");
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

        return $this->sendResponse($item, '{{$MODEL_NAME}} deleted successfully.');
    }
}
