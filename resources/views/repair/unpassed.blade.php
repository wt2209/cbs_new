@extends('header')
@section('title', '未通过项目')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">未通过项目</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('repair/unpassed') }}">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{isset($_GET['location']) ? $_GET['location'] : ''}}" name="location" placeholder="位置/房间号">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;或&nbsp;
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('repair/unpassed') }}' + '?' + sParam;
                            maskShow();
                            window.location = sUrl;
                            setTimeout(maskHide,2000);
                            return false;
                        })
                    </script>
                </form>
            </div>
        </div>
    </nav>
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
                <th>审核意见</th>
            </tr>
            </thead>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->content }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->report_at }}</td>
                    <td>{{ $item->reviewer }}</td>
                    <td>{{ $item->reviewed_at }}</td>
                    <td>{{ $item->review_remark }}</td>
                </tr>
            @endforeach
        </table>
        {!! $items->appends([
             'location'=>isset($_GET['location']) ? $_GET['location'] : '',
         ])->render() !!}
    </div>
@endsection
@section('modal')

@endsection
@section('bottom')
    <p>共有 {{$items->total()}} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection