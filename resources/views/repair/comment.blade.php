@extends('header')

@section('title', '评价')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/repair/create.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">评价</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('repair/index') }}"><< 返回未完工列表</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="{{ $item->id }}">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">位置/房间号</th>
                    <td width="20%">
                        {{ $item->location }}
                    </td>
                    <td width="10%"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>报修内容</th>
                    <td colspan="2" >
                        {{ $item->content }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>报修人</th>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>报修时间</th>
                    <td>
                        {{ $item->report_at }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>完工时间</th>
                    <td>
                        {{ $item->finished_at }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>评价</th>
                    <td>
                        <textarea name="comment" class="form-control"></textarea>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            </table>
            <div class="form-submit">
                <button class="btn btn-success" id="submit">提交</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        // 联系电话(手机/电话皆可)验证
        $.validator.addMethod("isTel", function(value,element) {
            var length = value.length;
            var mobile = /^(((1[0-9]{1}))+\d{9})$/;
            var tel = /^(\d{3,4}-?)?\d{7,9}$/g;
            return this.optional(element) || tel.test(value) || (length==11 && mobile.test(value));
        }, "请正确填写您的联系方式");

        /*表单验证*/
        var s = true;
        var validate = $("#form").validate({
            debug: false, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('repair/comment-store') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                comment:{
                    maxlength:255
                }
            },
            messages:{
                comment:{
                    maxlength:'不能多于255个字符！'
                }
            }
        });
    </script>
@endsection