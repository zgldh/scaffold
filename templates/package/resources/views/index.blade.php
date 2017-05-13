@extends('layouts.adminlte')

@section('title','$MODEL_NAME_PRESENTATION$')

@section('content')
@endsection

@section('scripts')
    <script>
        {{-- 如果有的话，在此输出前端立即需要的数据 --}}
    </script>
    @dist('vendor')
    @dist('$MODEL_IDENTIFIER$')
@endsection
