    /**
     *
@foreach($parameters as $parameter)
     * @param   {{$parameter}}
@endforeach
     * @return  JsonResponse
     */
    public function {{$actionName}}({{join(', ',$parameters)}})
    {
        $input = $request->all();

        $result = 'do something';

        return $this->sendResponse($result, 'success');
    }