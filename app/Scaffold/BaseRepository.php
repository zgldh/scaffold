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
     * @param null $filter
     * @return mixed
     */
    public function datatables($model = null, array $with = [], $filter = null)
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

        if (count($with) == 1) {
            foreach ($with as $key => $value) {
                if ($value === 'undefined') {
                    unset($with[$key]);
                }
            }
        }

        if (count($with) > 0) {
            $query = call_user_func_array([$query, 'with'], $with);
        }

        $dt = \DataTables::eloquent($query);
        $dt->escapeColumns('~');

        $columns = \Request::input('columns', []);
        if (sizeof($columns) > 0) {
            $dt->filter(function ($query) use ($columns, $filter) {
                if (is_callable($filter)) {
                    $filter($query);
                }
                foreach ($columns as $column) {
                    $columnNames = explode('.', $column['name']);
                    $advanceSearches = array_get($column, 'search.advance');
                    if ($advanceSearches) {
                        $query->where(function ($q) use ($advanceSearches, $columnNames) {
                            foreach ($advanceSearches as $operator => $value) {
                                $this->advanceSearch($q, $columnNames, $operator, $value);
                            }
                        });
                    }
                }
            }, true);
        } elseif ($filter) {
            $dt->filter($filter, true);
        }
        $result = $dt->make(true);
        $this->resetModel();
        return $result;
    }

    private function advanceSearch($query, $columnNames, $operator, $value)
    {
        if (sizeof($columnNames) == 1) {
            $columnName = $columnNames[0];
            if (is_array($value)) {
                switch ($operator) {
                    case '!=':
                        $query->whereNotIn($columnName, $value);
                        break;
                    case '=':
                        $query->whereIn($columnName, $value);
                        break;
                    default:
                        $query->where(function ($q) use ($columnName, $operator, $value) {
                            foreach ($value as $valueItem) {
                                $q->orWhere($columnName, $operator, $valueItem);
                            }
                        });
                }
            } else {
                $query->where($columnName, $operator, $value);
            }
        } else {
            $columnName = array_shift($columnNames);
            $query->whereHas($columnName, function ($q) use ($columnNames, $operator, $value) {
                $this->advanceSearch($q, $columnNames, $operator, $value);
            });
        }
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
                            $oldUploadObjects = $model->$key()->get();
                            foreach ($oldUploadObjects as $oldUploadObject) {
                                $oldUploadObject->uploadable()->dessociate();
                                $oldUploadObject->save();
                            }
                            // 2. Loop
                            foreach ($newValues as $newValue) {
                                $uploadId = $this->getUploadId($newValue);
                                $uploadObj = $uploadModelClassName::where('user_id', $userId)->where('id',
                                    $uploadId)->first();
                                if ($uploadObj) {
                                    // 3. Update this upload obj to associate to this $model, setup a proper type
                                    $uploadObj->type = $key;
                                    $model->$key()->save($uploadObj);
                                }
                            }
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
        if (is_array($item)) {
            return array_get($item, 'id', 0);
        }
        if (is_object($item)) {
            return object_get($item, 'id', 0);
        }
        return 0;
    }
}
