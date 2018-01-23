@extends('header')
@section('title', '未完工项目')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">未完工项目</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>

@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>位置/房间号</th>
                <th>报修内容</th>
                <th>报修人</th>
                <th>报修时间</th>
                <th>审核人</th>
                <th>审核时间</th>
                <th>审核说明</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($items as $item)
                {{--正在使用--}}
                <tr>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->content }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->report_at }}</td>
                    <td>{{ $item->reviewer }}</td>
                    <td>{{ $item->reviewed_at }}</td>
                    <td>{{ $item->review_remark }}</td>
                    <td>
                        <a href="javascript:;" class="btn btn-success btn-xs">打印</a>
                        <a href="{{ url('repair/finish/'.$item->id) }}" class="btn btn-warning btn-xs">完工</a>
                        <a href="{{ url('repair/cancel/'.$item->id) }}" class="btn btn-danger btn-xs">取消</a>
                        {{--<a href="javascript:;" delete_id="{{ $room->room_id }}" class="btn btn-danger btn-xs delete-button">删除</a>--}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
@section('modal')

@endsection
@section('bottom')

@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>


    </script>
@endsection