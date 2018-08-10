<?php namespace App\Scaffold\Datatables;

use Yajra\DataTables\EloquentDataTable;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2018/08/10
 * Time: 0:08
 */
class Builder
{
    /**
     * @var EloquentDataTable
     */
    private $datatables = null;

    public function __construct($query, $with = [])
    {
        $query = $this->applyWith($query, $with);

        $this->datatables = \DataTables::eloquent($query);
        $this->datatables->escapeColumns('~');
    }

    public function search($columns = [], $filterFunction = null)
    {
        if (sizeof($columns) > 0) {
            $this->datatables->filter(function ($query) use ($columns, $filterFunction) {
                if (is_callable($filterFunction)) {
                    $filterFunction($query);
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
        } elseif ($filterFunction) {
            $this->datatables->filter($filterFunction, true);
        }
        return $this;
    }

    /**
     * @param null $exportFileName
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function result($exportFileName = null)
    {
        if ($exportFileName) {
            // Excel

        } else {
            // JSON
            $result = $this->datatables->make(true);
            return $result;
        }
    }

    private function applyWith($query, $with = [])
    {
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
        return $query;
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
}
