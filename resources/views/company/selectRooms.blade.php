@extends('header')

@section('title', '新公司入住')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/add.css') }}"/>
    <style>
        .data-column{
            width:400px;
            border-right: 1px #ddd solid;
        }
    </style>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">新公司入住 - 选择房间</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="{{ url('company/select-rooms/'.$company_id) }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <p style="margin:15px 15px 0 15px;">请勾选要选择的房间并选定人数与性别信息</p>
            </div>
        </div>
    </nav>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form" method="post" action="{{ url('company/store-basic-info') }}">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="company_id" value="{{ $company_id }}">
        </form>
        <table class="table table-hover table-condensed">
            <tr id="row"></tr>
        </table>
    </div>
@endsection

@section('bottom')
    <button class="btn btn-success" id="submit">保存</button>
@endsection
@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        var bStatus = false;
        $(function(){
            maskShow();
            $.get('{{ url('room/empty-rooms-group-by-type') }}', '', function(data){
                var row = document.getElementById('row');
                for (var type in data) {
                    var rooms = data[type];
                    var column = document.createElement('td');
                    column.className = 'data-column'
                    var str = type + ':<br>';
                    str += '<div class="col-lg-2" >';
                    for (var i in rooms) {
                        var current = rooms[i];

                        str += '<div class="input-group">';
                        str += '<label class="input-group-addon">';
                        str += '<input type="checkbox"  value="'+current['room_id']+'">&nbsp;'+current['room_name'];
                        str += '</label>';
                        str += '<span class="input-group-addon">';
                        str += '<label class="no-bold"><input type="radio" value="1" checked name="gender['+current['room_id']+']">男</label>&nbsp;';
                        str += '<label class="no-bold"><input type="radio" value="2" name="gender['+current['room_id']+']">女</label>';
                        str += '</span>';
                        str += '<span class="input-group-addon">'+current['person_number']+'人间</span>';
                        str += '</div>';
                    }
                    str += '</div>';
                    column.innerHTML = str;
                    row.appendChild(column);
                }
                //为了使td宽度自适应
                row.appendChild(document.createElement('td'));
                maskHide()
            }, 'json')

            $('#submit').click(function(){
                var rooms = '';
                $('#row').find('input[type=checkbox]').each(function(){
                    if ($(this).prop('checked')) {
                        var iRoomId = $(this).val();
                        var iGender = 1;
                        $(this).parents('.input-group').find('input[type=radio]').each(function(){
                            if ($(this).prop('checked')) {
                                iGender = $(this).val();
                            }
                        });
                        //格式为：1_1 , 'room_id'_'gender',
                        rooms += iRoomId+'_'+iGender+'|';
                    }
                })

                rooms = rooms.substring(0, rooms.length - 1);
                var postStr = 'newCompany=1&rooms='+rooms;
                if (bStatus) {
                    return false;
                }
                bStatus = true;
                maskShow();
                $.post('{{ url('company/store-selected-rooms') }}', $('#form').serialize()+"&"+postStr, function(e){
                    maskHide();
                    if(e.status) {
                        //下一步：存储变动房间的水电底数
                        location.href = '{{ url("company-log/utility-of-changed-rooms") }}';
                    } else {
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = '{{ url("company/index") }}';
                        }});
                    }

                }, 'json');
            })
        })
    </script>
@endsection
