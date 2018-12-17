@extends('header')

@section('title', '备份列表')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">备份列表</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ route('backup.index') }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <p style="margin: 0;padding: 0 10px;line-height: 48px;">
            系统将自动删除60天以前的备份。
        </p>
    </nav>
@endsection
@section('content')
    <table class="table table-bordered table-hover table-condensed">
        <thead>
        <tr class="active">
            <th>文件名</th>
            <th>大小</th>
            <th>下载</th>
        </tr>
        </thead>
        @foreach($files as $file)
            <tr>
                <td>{{$file}}</td>
                <td>
                    {{round((\Storage::disk('backup')->size($file)) / 1024 / 1024, 2)}} M
                </td>
                <td>
                    <a href="{{route('backup.download', ['filename' => $file])}}" class="btn btn-success btn-xs">下载</a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
