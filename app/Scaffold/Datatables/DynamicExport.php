<?php namespace App\Scaffold\Datatables;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2018/08/13
 * Time: 0:08
 */
class DynamicExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private $query = null;
    private $columns = [];

    public function __construct($query, $columns = [])
    {
        $this->query = $query;
        $this->columns = $columns;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array_map(function ($item) {
            return $item['label'];
        }, $this->columns);
    }

    /**
     * @var $row
     * @return array
     */
    public function map($row): array
    {
        return array_map(function ($item) use ($row) {
            $columnName = $item['name'];
            return object_get($row, $columnName);
        }, $this->columns);
    }
}
