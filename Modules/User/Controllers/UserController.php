<?php namespace Modules\User\Controllers;

use App\Scaffold\AppBaseController;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Upload\Repositories\UploadRepository;
use Modules\Upload\Requests\CreateUploadRequest;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;
use Modules\User\Requests\CreateUserRequest;
use Modules\User\Requests\UpdateUserRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;

class UserController extends AppBaseController
{

    public function __construct(UserRepository $userRepo)
    {
        $this->repository = $userRepo;
        $this->middleware("auth:api");
        $this->middleware("permission.auto")->except([
            'current',
            'putMobile',
            'putGender',
            'putPassword',
            'postAvatar',
        ]);
    }

    /**
     * Display a listing of the User.
     * @name 列表
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
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $user = $this->repository->create($input);
        $user->load($request->getWith());

        return $this->sendResponse($user, 'User saved successfully.');
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $user = $this->repository->findWithoutFail($id);
        $user->load($request->getWith());

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse($user, '');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  int $id
     * @param UpdateUserRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->repository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user = $this->repository->update($request->all(), $id);
        $user->load($request->getWith());

        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $user = $this->repository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $this->repository->delete($id);

        return $this->sendResponse($user, 'User deleted successfully.');
    }

    /**
     * @no-permission
     * @return JsonResponse
     */
    public function current()
    {
        $user = \Auth::user();
        $data = $user->toArray();
        $data['avatar'] = $user->avatar ? $user->avatar->url : null;
        $data['roles'] = $user->roles->pluck('name');
        $data['permissions'] = $user->getAllPermissions()->pluck('name');
        return $this->sendResponse($data, 'current user');
    }

    /**
     *
     * @no-permission
     * @param      \Modules\User\Requests\PutMobileRequest $request
     * @return    JsonResponse
     */
    public function putMobile(\Modules\User\Requests\PutMobileRequest $request)
    {
        $mobile = $request->get('mobile');

        $user = $request->user('api');
        $user->mobile = $mobile;
        $user->save();

        return $this->sendResponse(null, 'success');
    }

    /**
     *
     * @no-permission
     * @param      \Modules\User\Requests\PutGenderRequest $request
     * @return    JsonResponse
     */
    public function putGender(\Modules\User\Requests\PutGenderRequest $request)
    {
        $gender = $request->get('gender');

        $user = $request->user('api');
        $user->gender = $gender;
        $user->save();

        return $this->sendResponse(null, 'success');
    }

    /**
     *
     * @no-permission
     * @param      \Modules\User\Requests\PutPasswordRequest $request
     * @return    JsonResponse
     */
    public function putPassword(\Modules\User\Requests\PutPasswordRequest $request)
    {
        $oldPassword = $request->get('oldPassword');
        $password = $request->get('password');

        $user = $request->user('api');
        if (\Hash::check($oldPassword, $user->password)) {
            $user->password = $password;
            $user->save();
            return $this->sendResponse(null, 'success');
        } else {
            return $this->sendError(__('passwords.bad_old'), 422);
        }
    }

    /**
     *
     * @no-permission
     * @param CreateUploadRequest $request
     * @param UploadRepository $repository
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function postAvatar(CreateUploadRequest $request, UploadRepository $repository)
    {
        $input = $request->all();
        $user = $request->user();

        if ($request->has('user_id')) {
            if (!$user->hasPermissionTo('manage-user', 'api')) {
                throw new AuthorizationException();
            }
            $user = User::find($input['user_id']);
        } else {
            $input['user_id'] = $user->id;
        }

        $upload = $repository->createAvatar($input, $user);
        $upload->load($request->getWith());

        return $this->sendResponse($upload, 'Upload saved successfully.');
    }
}
