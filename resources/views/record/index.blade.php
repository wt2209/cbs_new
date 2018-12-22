@extends('header')
@section('title', '房间使用记录')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
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
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('record/search') }}">
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <select name="company_id" class="form-control">
                            <option value="0">--选择公司--</option>
                            @foreach($companies as $company)
                                <option value="{{$company->company_id}}"
                                    @if(isset($_GET['company_id'])&&$_GET['company_id'] == $company->company_id)
                                        selected=""
                                    @endif>
                                    {{$company->company_name}}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" name="room" placeholder="房间号" value="{{isset($_GET['room'])?$_GET['room']:''}}">
                        <select name="in_use" class="form-control">
                            <option value="2" @if(isset($_GET['in_use'])&&$_GET['in_use'] == 2) selected=""@endif>全部</option>
                            <option value="1" @if(isset($_GET['in_use'])&&$_GET['in_use'] == 1) selected=""@endif>正在使用</option>
                            <option value="0" @if(isset($_GET['in_use'])&&$_GET['in_use'] == 0) selected=""@endif>已退房</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;或&nbsp;
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('record/search') }}' + '?' + sParam;
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
                <th>公司名</th>
                <th>房间号</th>
                <th>属于</th>
                <th>性别</th>
                <th>月租金</th>
                <th>入住时间</th>
                <th>退房时间</th>
                <th>入住时电表</th>
                <th>入住时水表</th>
                <th>退房时电表</th>
                <th>退房时水表</th>
                <th>操作</th>
            </tr>
            </thead>

            @foreach($records as $record)
                <tr>
                    <td>{{ $record->company->company_name }}</td>
                    <td>{{ $record->room->room_name }}</td>
                    <td>{{ $record->belongs_to ? $record->belongs_to : $record->company->belongs_to }}</td>
                    <td>{{ $record->gender }}</td>
                    <td>{{ $record->price }}</td>
                    <td>{{ $record->entered_at }}</td>
                    <td>{{ $record->quit_at }}</td>
                    <td>{{ $record->enter_electric_base }}</td>
                    <td>{{ $record->enter_water_base }}</td>
                    <td>{{ $record->in_use == 1 ? '' : $record->quit_electric_base }}</td>
                    <td>{{ $record->in_use == 1 ? '' : $record->quit_water_base }}</td>
                    <td>
                        <a href="{{url('record/edit', $record->id)}}" class="btn btn-success btn-xs">修改</a>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $records->appends([
                'company_id'=>isset($_GET['company_id']) ? $_GET['company_id'] : 0,
                'in_use'=>isset($_GET['in_use']) ? $_GET['in_use'] : 2,
                'room'=>isset($_GET['room']) ? $_GET['room'] : '',
            ])->render() !!}
    </div>
@endsection
@section('bottom')
    <p>共有 {{$records->total()}} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection