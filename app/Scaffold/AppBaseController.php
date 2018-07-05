<?php namespace App\Scaffold;

use App\Http\Controllers\Controller;
use App\Http\Requests\BundleRequest;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\IndexRequest;

/**
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * @param $result
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message)
    {
        return \Response::json([
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
        return \Response::json([
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
        $data = [];
        $messages = [];
        foreach ($indexes as $index) {
            try {
                $data[] = [
                    'index' => $index,
                    'data'  => call_user_func_array([$this->repository, $action], [$index, $options])
                ];
            } catch (\Exception $e) {
                $messages[] = [
                    'index'   => $index,
                    'message' => $e->getMessage()
                ];
            }
        }
        return $this->sendResponse($data, $messages);
    }
}
