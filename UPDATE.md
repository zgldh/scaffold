## Update to 1.6 from 1.5

1. Since the `dynamicRouterMap.js` file is updated. I suggest you to update it too.
    But if you don't want, this system can still running.

   So these guys wanna do updating, please do following twists:

    1. Create `frontend\src\router\user.js` `frontend\src\router\upload.js` `frontend\src\router\activityLog.js` `frontend\src\router\setting.js`

        These file names are based on your modules name in camelCase.

        And fill them with the routes configurations data copied from your original `dynamicRouterMap.js` file.

    2. Then Update your `frontend\src\router\dynamicRouterMap.js`, make it looks like:

        ```
        export default [
          ...require('./user').default,
          ...require('./upload').default,
          ...require('./activityLog').default,
          ...require('./setting').default
          // Append More Routes. Don't remove me
        ]
        ```

    3. Routes done.

2. `php artisan migrate` to create the new `z_settings` table.

3. Done.

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