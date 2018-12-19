@extends('header')

@section('title', '修改入住记录')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">修改入住记录</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('record/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="{{ $record->id }}"/>
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">公司名</th>
                    <td width="20%">
                        {{ $record->company->company_name }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">房间号</th>
                    <td width="20%">
                        {{ $record->room->room_name }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">属于</th>
                    <td width="20%">
                        <select name="belongs_to" class="form-control">
                            <option value="造船" @if ($record->belongs_to == '造船')selected @endif>造船</option>
                            <option value="修船" @if ($record->belongs_to == '修船')selected @endif>修船</option>
                        </select>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">月租金</th>
                    <td width="20%">
                        <input type="text" class="form-control" name="price" value="{{ $record->price }}">
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">入住时电表底数</th>
                    <td width="20%">
                        <input type="text" class="form-control" name="enter_electric_base" value="{{ $record->enter_electric_base }}">
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">入住时水表底数</th>
                    <td width="20%">
                        <input type="text" class="form-control" name="enter_water_base" value="{{ $record->enter_water_base }}">
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>
                <tr class="no-border">
                    <th width="10%">入住时间</th>
                    <td width="20%">
                        <input type="text" class="form-control" name="entered_at" value="{{ $record->entered_at }}">
                    </td>
                    <td width="10%"></td>
                    <td></td>
                </tr>

                @if ($record->in_use == 0) 
                    <tr class="no-border">
                        <th width="10%">退房时电表底数</th>
                        <td width="20%">
                            <input type="text" class="form-control" name="quit_electric_base" value="{{ $record->quit_electric_base }}">
                        </td>
                        <td width="10%"></td>
                        <td></td>
                    </tr>
                    <tr class="no-border">
                        <th width="10%">退房时水表底数</th>
                        <td width="20%">
                            <input type="text" class="form-control" name="quit_water_base" value="{{ $record->quit_water_base }}">
                        </td>
                        <td width="10%"></td>
                        <td></td>
                    </tr>
                    <tr class="no-border">
                        <th width="10%">退房时间</th>
                        <td width="20%">
                            <input type="text" class="form-control" name="quit_at" value="{{ $record->quit_at }}">
                        </td>
                        <td width="10%"></td>
                        <td></td>
                    </tr>
                @endif
            
            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">提 交</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        var s = true;
        var validate = $("#form").validate({
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('record/update') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            }
        });
    </script>
@endsection