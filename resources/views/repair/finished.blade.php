@extends('header')
@section('title', '已完工项目')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">已完工项目</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>

    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('repair/finished') }}">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{isset($_GET['location']) ? $_GET['location'] : ''}}" name="location" placeholder="位置/房间号">&nbsp;&nbsp;&nbsp;
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{isset($_GET['year_month']) ? $_GET['year_month'] : ''}}" name="year_month" placeholder="月份，格式为：2016-3">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;或&nbsp;
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('repair/finished') }}' + '?' + sParam;
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
                <th>审核说明</th>
                <th>完工时间</th>
                <th>完工说明</th>
                <th>评价</th>
                <th>操作</th>
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
                    <td>{{ $item->finished_at }}</td>
                    <td>{{ $item->finish_remark }}</td>
                    <td>{{ $item->comment }}</td>
                    <td>
                        <a href="{{ url('repair/comment/'.$item->id) }}" class="btn btn-success btn-xs">评价</a>
                        <button delete_id="{{ $item->id }}"  class="btn btn-danger btn-xs delete-button">删除</button>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $items->appends([
               'year_month'=>isset($_GET['year_month']) ? $_GET['year_month'] : '',
               'location'=>isset($_GET['location']) ? $_GET['location'] : '',
           ])->render() !!}
    </div>
@endsection
@section('modal')
    <!-- delete modal -->
    <div id="delete-modal" class="modal bs-example-modal-sm fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">删除确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要删除吗？
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="delete-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <p>共有 {{$items->total()}} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //模态框删除
        ajaxDelete('{{ url('repair/delete') }}');

    </script>
@endsection