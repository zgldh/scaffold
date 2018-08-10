## Update to 1.5 from 1.4

1. Change all your `index()` methods of all Controllers,

    from

    ```
    public function index(IndexRequest $request)
    {
        $with = $request->getWith();
        $data = $this->repository->datatables(null, $with);

        return $data;
    }
    ```

    to

    ```
    public function index(IndexRequest $request)
    {
        $data = $this->repository->datatables(null, $request->getWith())
            ->search($request->getColumns(), null)
            ->result($request->getExportFileName());

        return $data;
    }
    ```

2. Delete file `app\Http\Middleware\ExportDatatables.php`
3. Delete `ExportDatatables::class,` from `App\Http\Kernel.php` `$middleware` configuration.