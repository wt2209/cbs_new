@extends('header')

@section('title', '减少房间')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/add.css') }}"/>
    <style>
        .item{
            overflow:hidden;
            padding:2px 3px;
        }
    </style>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">减少房间</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="{{ url('company/delete-rooms/'.$company->company_id) }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <p style="margin:15px 15px 0 15px;">请勾选要减少的房间</p>
            </div>
        </div>
    </nav>
@endsection
@section('content')
    <div class="table-responsive">
        <form class="form-inline">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="company_id" value="{{ $company->company_id }}">
            <div id="data-container">
                <br>
                @foreach ($rooms as $room)
                    <div class="item">
                        <div class="checkbox room-name">
                            <label>
                                <input type="checkbox" name="room[{{$room['room_id']}}]"> {{$room['room_name']}} ({{$room['person_number']}}人间)
                                <input type="hidden" class="room" value="{{$room['room_id']}}">
                            </label>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="form-group electric-base">
                            <input type="text" class="form-control electric" name="electric[{{$room['room_id']}}]" placeholder="电表底数">
                        </div>
                        <div class="form-group water-base">
                            <input type="text" class="form-control water" name="water[{{$room['room_id']}}]" placeholder="水表底数">
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
@endsection

@section('bottom')
    <button class="btn btn-success" id="submit">保存</button>
@endsection
@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        var bStatus = false;
        $(function(){
            $('#submit').click(function(){
                var rooms = '';
                
                var items = $('.item').each(function(){
                    var self = $(this);
                    if (self.find('input[type=checkbox]').prop('checked')) {
                        var roomId = self.find('.room')[0].value;
                        
                        var water = self.find('.water').val();
                        water = water == '' ? 0 : water;

                        var electric = self.find('.electric').val();
                        electric = electric == '' ? 0 : electric;
                    
                        rooms += roomId+'_'+electric+'_'+water+'|';
                    }
                })
                
                rooms = rooms.substring(0, rooms.length - 1);
                // console.log(rooms);
                var postStr = '_token=' + $('input[name=_token]').val();
                postStr += '&company_id=' + $('input[name=company_id]').val();
                postStr += '&rooms='+rooms;
                if (bStatus) {
                    return false;
                }
                bStatus = true;
                maskShow();
                $.post('{{ url('record/mass-complete') }}', postStr, function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                        /*返回并刷新原页面*/
                        location.href = '{{ url("company/index") }}';
                    }});

                }, 'json');
            })
        })
    </script>
@endsection
