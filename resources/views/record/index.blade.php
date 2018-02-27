@extends('header')
@section('title', '房间使用记录')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/utility/base.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">房间使用记录</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('utility/base-search') }}">
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                        <select name="company_id" class="form-control">
                            <option value="0">--选择公司--</option>
                            @foreach($companies as $company)
                                <option value="{{$company->company_id}}">{{$company->company_name}}</option>
                            @endforeach
                        </select>

                        <select name="in_use" class="form-control">
                            <option value="2">全部</option>
                            <option value="1">正在使用</option>
                            <option value="0">已退房</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;或&nbsp;
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('utility/base-search') }}' + '?' + sParam;
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
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:location='{{ url('utility/add') }}';">录入底数</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>公司名</th>
                <th>房间号</th>
                <th>性别</th>
                <th>月租金</th>
                <th>入住时间</th>
                <th>退房时间</th>
                <th>入住时电表</th>
                <th>入住时水表</th>
                <th>退房时电表</th>
                <th>退房时水表</th>
            </tr>
            </thead>

            @foreach($records as $record)
                <tr>
                    <td>{{ $record->company->company_name }}</td>
                    <td>{{ $record->room->room_name }}</td>
                    <td>{{ $record->gender }}</td>
                    <td>{{ $record->price }}</td>
                    <td>{{ $record->entered_at }}</td>
                    <td>{{ $record->quit_at }}</td>
                    <td>{{ $record->enter_electric_base }}</td>
                    <td>{{ $record->enter_water_base }}</td>
                    <td>{{ $record->quit_electric_base }}</td>
                    <td>{{ $record->quit_water_base }}</td>
                </tr>
            @endforeach
        </table>
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
    
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        //模态框删除
        ajaxDelete('{{ url('utility/base-delete/') }}');
    </script>
@endsection