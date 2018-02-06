@extends('header')
@section('title', '房间明细')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">房间明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header" style="overflow: visible;">

                <form class="navbar-form navbar-left" role="search"  method="get" action="{{ url('room/search') }}">
                    <div class="form-group dropdown" style="margin-right:80px;">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            选择楼号
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            @foreach($structure as $type=>$buildings)
                                <li class="dropdown-header">{{ $type }}</li>
                                    @foreach ($buildings as $building)
                                        <li><a href="{{action('RoomController@getIndex', ['typename'=>$type, 'building'=>$building])}}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $building }}</a></li>
                                    @endforeach
                                <li role="separator" class="divider"></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="form-group" >
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="room_type" value="1">
                        <input type="text" class="form-control"  value="{{ $_GET['room_name'] or '' }}" name="room_name" placeholder="房间号">&nbsp;或者
                        <select name="room_status" class="form-control">
                            <option value="0">全部房间</option>
                            <option value="1" @if(isset($_GET['room_status'])&&$_GET['room_status'] == 1) selected=""@endif>正在使用</option>
                            <option value="2" @if(isset($_GET['room_status'])&&$_GET['room_status'] == 2) selected=""@endif>空房间</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success btn-sm export">导出所有房间到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sUrl = '{{ url('room/export') }}';
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
        @if(isset($currentType)&&isset($currentBuilding))
        <p>当前位置：{{ $currentType}} >> {{$currentBuilding}}</p>
        @endif
    </div>
@endsection
@section('content')
    @foreach($roomsWithFloor as $key => $building)
        <div class="building">
            @foreach($building as $floor)
                <div class="floor">
                    @foreach($floor as $room)
                        <div class="room">
                            <div class="title">
                                <h3>
                                    {{$room->room_name}}
                                </h3>
                            </div>
                            <div class="room-content">
                                <div class="company">
                                    {{ isset($room->company->company_name) ? $room->company->company_name : "" }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
@section('modal')
    <!-- delete modal -->
    <div id="delete-modal" class="modal fade bs-example-modal-sm">
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
    <p><br>当前共有 {{ $count['total'] }} 套房间。其中，占用 <span style="color:red">{{ $count['used'] }}</span> 套，空房 <span style="color:red">{{ $count['empty'] }}</span> 套。</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        //删除模态框
        ajaxDelete('{{ url('room/remove/') }}');

    </script>
@endsection