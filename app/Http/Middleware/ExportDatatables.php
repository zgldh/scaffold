<?php

namespace App\Http\Middleware;

use Closure;
use League\Csv\Writer;

class ExportDatatables
{
    const KEY_NAME = '_export';
    const MAX_LENGTH = 999999;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $exportFileName = $request->get(self::KEY_NAME);
        if ($exportFileName) {
            $this->setGlobalRequest();
        }
        $response = $next($request);
        if ($exportFileName) {
            $columns = $this->getColumns($request);
            $response = $this->getDownloadResponse($response, $columns, $exportFileName);
        }

        return $response;
    }

    private function setGlobalRequest()
    {
        $globalRequest = app('request');
        $globalRequest->query->set('start', 0);
        $globalRequest->query->set('length', self::MAX_LENGTH);
    }

    private function getColumns($request)
    {
        $columns = [];
        foreach ($request->get('columns') as $column) {
            if ($column["label"]) {
                $columns[$column["data"]] = $column["label"];
            }
        }
        return $columns;
    }

    private function getDownloadResponse($rawResponse, $columns, $exportFileName)
    {
        $format = strtolower(array_last(explode('.', $exportFileName)));
        $csvContent = 'exported_file';
        if ($format === 'csv') {
            $data = json_decode($rawResponse->getContent());
            if (!isset($data->data)) {
                \Log::error("Can not find data in response: ", [$rawResponse]);
                return $rawResponse;
            }
            $csvContent = $this->exportCSV($data->data, $columns);
        }
        $response = response($csvContent);

        return $response;
    }

    private function exportCSV($data, $columns)
    {
        $header = array_values($columns);
        $columnKeys = array_keys($columns);
        $records = array_map(function ($rawItem) use ($columnKeys) {
            return array_map(function ($columnKey) use ($rawItem) {
                return object_get($rawItem, $columnKey);
            }, $columnKeys);
        }, $data);

        //load the CSV document from a string
        $csv = Writer::createFromString('');

        //insert the header
        $csv->insertOne($header);

        //insert all the records
        $csv->insertAll($records);

        return $csv->getContent();
    }
}
