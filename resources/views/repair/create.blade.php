@extends('header')

@section('title', '报修')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/repair/create.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">报修</a></li>
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
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th width="10%">位置/房间号</th>
                    <td width="20%">
                        <input type="text" class="form-control input-sm" name="location"/>
                    </td>
                    <td width="10%"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>报修内容</th>
                    <td colspan="2" >
                        <textarea name="content" class="form-control" cols="30" rows="3"></textarea>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>报修人</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="name"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>联系人电话</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="phone_number"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>时间</th>
                    <td>
                        <input type="text" class="form-control input-sm" name="report_at" placeholder="格式：2018-1-22 留空则以当前日期为准"/>
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
                    $.post('{{ url('repair/store') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status});
                        if (e.status) {
                            window.parent.getRepairNotify();
                            $('#form')[0].reset();
                        }
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                location:{
                    required:true,
                    maxlength:255
                },
                content:{
                    required:true,
                    maxlength:255
                },
                name:{
                    maxlength:5
                },
                phone_number:{
                    isTel:true
                }
            },
            messages:{
                location:{
                    required:'必须填写！',
                    maxlength:'不能多于255个字符！'
                },
                content:{
                    required:'必须填写！',
                    maxlength:'不能多于255个字符！'
                },
                name:{
                    maxlength:'不能多于5个字符！'
                },
                phone_number:{
                    isTel:'请填写一个正确的电话号码！'
                }
            }
        });
    </script>
@endsection