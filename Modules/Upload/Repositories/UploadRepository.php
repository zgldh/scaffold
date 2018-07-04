<?php

namespace Modules\Upload\Repositories;

use App\Scaffold\BaseRepository;
use Modules\Upload\Models\Upload;
use Modules\User\Models\User;
use zgldh\UploadManager\UploadException;
use zgldh\UploadManager\UploadManager;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

class UploadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'path'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Upload::class;
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $user = \Auth::user();
        if ($user) {
            $this->scopeQuery(function ($query) use ($user) {
                if (!$user->isAdmin()) {
                    $query->where('user_id', $user->id);
                }
                return $query;
            });
        }
    }

    /**
     * @no-permission
     */
    public function create(array $attributes, Model $associate = null)
    {
        unset($attributes['path']);
        unset($attributes['size']);

        $uploadManager = UploadManager::getInstance();
        $upload = $uploadManager->withDisk($attributes['disk'])->upload($attributes['file']);
        if (!$upload) {
            throw new UploadException($uploadManager->getErrors());
        }

        $upload->name = @$attributes['name'] ?: mb_convert_encoding($attributes['file']->getClientOriginalName(), 'utf8');
        $upload->description = @$attributes['description'] ?: '';
        $upload->type = @$attributes['type'] ?: '';
        $upload->user_id = $attributes['user_id'];
        if ($associate) {
            $upload->uploadable_type = get_class($associate);
            $upload->uploadable_id = $associate->id;
        }
        $upload->save();
        return $upload;
    }

    /**
     * @no-permission
     */
    public function createAvatar(array $attributes, User $user)
    {
        $upload = \DB::transaction(function () use ($attributes, $user) {
            if ($user->avatar) {
                $user->avatar->delete();
            }
            $attributes['type'] = 'avatar';
            $upload = $this->create($attributes, $user);
            return $upload;
        });
        return $upload;
    }

    /**
     * @no-permission
     */
    public function update(array $attributes, $id)
    {
        unset($attributes['user_id']);
        unset($attributes['path']);
        unset($attributes['size']);
        unset($attributes['uploadable_id']);
        unset($attributes['uploadable_type']);

        $upload = $this->find($id);
        if (isset($attributes['name'])) {
            $upload->name = $attributes['name'];
        }
        if (isset($attributes['description'])) {
            $upload->description = @$attributes['description'] ?: '';
        }
        if (isset($attributes['type'])) {
            $upload->type = @$attributes['type'] ?: '';
        }
        if (isset($attributes['file'])) {
            $disk = @$attributes['disk'] ?: $upload->disk;
            $uploadManager = UploadManager::getInstance();
            $uploadManager->withDisk($disk)->update($upload, $attributes['file']);
        };
        $upload->save();

        return $upload;
    }

    /**
     * @no-permission
     */
    public function cleanUpUpload($userId)
    {
        $query = call_user_func($this->model() . '::query');
        $uploads = $query->where('user_id', $userId)->whereNull('uploadable_id')->get();
        foreach ($uploads as $upload) {
            $upload->delete();
        }
    }
}
