<?php namespace App\Scaffold;

use Carbon\Carbon;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Response;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2016/12/30
 * Time: 0:08
 */
abstract class BaseRepository extends \Prettus\Repository\Eloquent\BaseRepository
{
    private $uploadFileExpireHours = 12;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->scopeQuery(function ($model) {
            return $this->getModuleScope($model);
        });
    }

    /**
     * 模型Scope
     */
    protected function getModuleScope($model)
    {
        return $model;
    }

    /**
     * @no-permission
     * @param $model
     * @param array $with
     * @return Datatables\Builder
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function datatables($model = null, array $with = [])
    {
        if ($model == null) {
            $this->applyScope();
            $query = $this->model;
        } elseif (is_a($model, Builder::class)) {
            $query = $model;
        } elseif (is_a($model, \Illuminate\Database\Eloquent\Builder::class)) {
            $query = $model;
        } elseif (is_a($model, Model::class)) {
            $query = $model->newQuery();
        } elseif (is_string($model)) {
            $query = call_user_func($model . '::query');
        } else {
            $this->applyScope();
            $query = $this->model;
        }

        if (is_a($query, Model::class)) {
            $query = $query->newQuery();
        }

        $builder = new \App\Scaffold\Datatables\Builder($query, $with);
        $this->resetModel();
        return $builder;
    }

    // Inherit from InfyOm\Generator\Common\BaseRepository

    public function findWithoutFail($id, $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @no-permission
     */
    public function create(array $attributes)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        $result = $this->parserResult($model);


        $primaryKey = $model->getKey();
        $query = $model->newQuery();
        $query->withoutGlobalScopes();
        $model = $query->find($primaryKey);
        return $model;
    }

    /**
     * @no-permission
     */
    public function update(array $attributes, $id)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }


    /**
     * @no-permission
     */
    public function updateRelations($model, $attributes)
    {
        $uploadModelClassName = config('upload.upload_model');
        $dealWithUpload = false;
        foreach ($attributes as $key => $val) {
            if (isset($model) &&
                method_exists($model, $key) &&
                is_a(@$model->$key(), 'Illuminate\Database\Eloquent\Relations\Relation')
            ) {
                $relation = $model->$key($key);

                // 处理上传
                $isRelatedUpload = false;
                if ($uploadModelClassName && is_a($relation->getRelated(), $uploadModelClassName)) {
                    $isRelatedUpload = true;
                    $dealWithUpload = true;
                }

                $methodClass = get_class($relation);
                switch ($methodClass) {
                    case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
                        $newValues = array_get($attributes, $key, []);
                        $newValues = $newValues ?: [];
                        if (array_search('', $newValues) !== false) {
                            unset($newValues[array_search('', $newValues)]);
                        }

                        if (count($newValues) && (is_object($newValues[0]) || is_array($newValues[0]))) {
                            $newValues = array_map(function ($value) {
                                return is_array($value) ? array_get($value, 'id') : object_get($value, 'id');
                            }, $newValues);
                        }

                        $model->$key()->sync(array_values($newValues));
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
                        $modelKey = $model->$key()->getForeignKey();
//                        $newValue = array_get($attributes, $modelKey, null);
                        $newValue = array_get($attributes, $key, null);
                        $newValue = $newValue == '' ? null : $newValue;
                        $newValue = is_array($newValue) ? array_get($newValue, 'id', null) : $newValue;
                        $model->$modelKey = $newValue;
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOne':
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOneOrMany':
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasMany':
                        $newValues = array_get($attributes, $key, []);
                        $newValues = $newValues ?: [];
                        if (array_search('', $newValues) !== false) {
                            unset($newValues[array_search('', $newValues)]);
                        }
                        $modelKey = $model->$key($key)->getForeignKeyName();

                        foreach ($newValues as $index => $newValue) {
                            $newValues[$index] = $newValue['id'];
                        }

                        foreach ($model->$key as $rel) {
                            if (!in_array($rel->id, $newValues)) {
                                $rel->$modelKey = null;
                                $rel->save();
                            }
                        }

                        if (count($newValues) > 0) {
                            $related = get_class($model->$key()->getRelated());
                            foreach ($newValues as $val) {
                                if (is_string($val) || is_numeric($val)) {
                                    $rel = $related::find($val);
                                    $rel->$modelKey = $model->id;
                                    $rel->save();
                                }
                            }
                        }
                        break;
                }

                if ($isRelatedUpload) {
                    $userId = \Auth::user()->id;
                    switch ($methodClass) {
                        case 'Illuminate\Database\Eloquent\Relations\MorphOne':
                            $newValue = array_get($attributes, $key, null);
                            $uploadId = $this->getUploadId($newValue);
                            // 1. Check upload obj is uploaded by current user
                            $uploadObj = $uploadModelClassName::where('user_id', $userId)->where('id',
                                $uploadId)->first();
                            if ($uploadObj) {
                                $oldUploadObject = $model->$key()->where('id', '<>', $uploadId)->first();
                                if ($oldUploadObject) {
                                    // 2. Remove old one.
                                    $oldUploadObject->delete();
                                }
                                // 3. Update this upload obj to associate to this $model, setup a proper type
                                $uploadObj->type = $key;
                                $model->$key()->save($uploadObj);
                            }
                            break;
                        case 'Illuminate\Database\Eloquent\Relations\MorphMany':
                            $newValues = array_get($attributes, $key, []);
                            // 1. Unassociate old uploads to this $model
                            $model->$key()->update(['uploadable_id' => null, 'uploadable_type' => null]);
                            // 2. Loop
                            $uploadIds = array_map(function ($newValue) {
                                return $this->getUploadId($newValue);
                            }, $newValues);
                            $uploadModelClassName::where('user_id', $userId)->whereIn('id', $uploadIds)
                                ->update(['uploadable_id'   => $model->getKey(),
                                          'uploadable_type' => get_class($model),
                                          'type'            => $key]);
                            break;
                    }
                }
            }
        }
        // 处理上传
        if ($dealWithUpload && $userId) {
            $expireTime = Carbon::now()->addHours(-$this->uploadFileExpireHours);
            $query = call_user_func($uploadModelClassName . '::query');
            $uploads = $query
                ->where('user_id', $userId)
                ->whereNull('uploadable_id')
                ->where('created_at', '<', $expireTime)->get();
            foreach ($uploads as $upload) {
                $upload->delete();
            }
        }

        return $model;
    }

    private function getUploadId($item)
    {
        if (is_numeric($item)) {
            return $item;
        }
        return data_get($item, 'id', 0);
    }
}
