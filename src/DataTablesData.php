<?php namespace zgldh\Scaffold;

use Yajra\Datatables\Facades\Datatables;

trait DataTablesData
{
    public function dataTableData()
    {
        $query = call_user_func($this->model() . '::query');
        return Datatables::eloquent($query)->make(true);
    }
}
