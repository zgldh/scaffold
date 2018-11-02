<?php namespace Modules\Post\Controllers;

use Modules\Post\Requests\CreatePostRequest;
use Modules\Post\Requests\UpdatePostRequest;
use Modules\Post\Repositories\PostRepository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;

class PostController extends AppBaseController
{

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("auth:api");
        $this->middleware("permission.auto");
    }

    /**
     * Display a listing of the Post.
     *
     * @param  IndexRequest $request
     * @return  JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws  \Exception
     */
    public function index(IndexRequest $request)
    {
        $data = $this->repository->datatables(null, $request->getWith())
            ->search($request->getColumns(), null)
            ->result($request->getExportFileName());

        return $data;
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param  CreatePostRequest $request
     * @return  JsonResponse
     */
    public function store(CreatePostRequest $request)
    {
        $input = $request->all();

        $item = $this->repository->create($input);
        $item->load($request->getWith());

        return $this->sendResponse($item, 'Post saved successfully.');
    }

    /**
     * Display the specified Post.
     *
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $item = $this->repository->findWithoutFail($id);
        if (empty($item)) {
            return $this->sendError('Post not found');
        }
        $item->load($request->getWith());

        return $this->sendResponse($item, '');
    }

    /**
     * Update the specified Post in storage.
     *
     * @param    int $id
     * @param  UpdatePostRequest $request
     *
     * @return  JsonResponse
     */
    public function update($id, UpdatePostRequest $request)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('Post not found');
        }

        $item = $this->repository->update($request->all(), $id);
        $item->load($request->getWith());

        return $this->sendResponse($item, 'Post updated successfully.');
    }

    /**
     * Remove the specified Post from storage.
     *
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function destroy($id)
    {
        $item = $this->repository->findWithoutFail($id);

        if (empty($item)) {
            return $this->sendError('Post not found');
        }

        $this->repository->delete($id);

        return $this->sendResponse($item, 'Post deleted successfully.');
    }
}
