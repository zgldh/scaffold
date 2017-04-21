<?php namespace zgldh\Scaffold;

use App\Http\Controllers\Controller;
use App\Http\Requests\BundleRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Response;
use Yajra\Datatables\Facades\Datatables;

/**
 * Class AppBaseController
 */
class  AppBaseController extends Controller
{
    protected $repository;

    /**
     * @param $result
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message)
    {
        return Response::json([
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ]);
    }

    /**
     * @param $error
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $code = 404)
    {
        return Response::json([
            'success' => false,
            'message' => $error,
        ], $code);
    }

    /**
     * 批量操作
     * 如果想让一个 Repository 的函数支持批量操作，则要求该函数第一个参数一定是对象的ID
     * @param BundleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bundle(BundleRequest $request)
    {
        $action = $request->input('action');
        if (!method_exists($this->repository, $action)) {
            return $this->sendError("Action {$action} does not exist.");
        }
        $indexes = $request->input('indexes');
        $options = $request->input('options');
        $results = [];
        $messages = [];
        foreach ($indexes as $index) {
            try {
                $results[$index] = call_user_func_array([$this->repository, $action], [$index, $options]);
            } catch (\Exception $e) {
                $results[$index] = false;
                $messages[] = $e->getMessage();
            }
        }
        return $this->sendResponse($results, $messages);
    }
}
