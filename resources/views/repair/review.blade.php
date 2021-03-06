@extends('header')

@section('title', '审核维修项目')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/repair/create.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">审核维修项目</a></li>
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
            <input type="hidden" name="id" value="{{$item->id}}">
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
                    <th>是否通过</th>
                    <td colspan="2" >
                        <label><input type="radio" name="is_passed" value="1" checked> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" name="is_passed" value="0"> 否</label>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>审核意见</th>
                    <td colspan="2" >
                        <textarea name="review_remark" class="form-control"></textarea>
                    </td>
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
                    $.post('{{ url('repair/review-for-one') }}', $('#form').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            window.parent.getRepairNotify();
                            /*返回并刷新原页面*/
                            location.href = document.referrer;
                        }});
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                review_remark:{
                    maxlength:255
                }
            },
            messages:{
                review_remark:{
                    maxlength:'不能多于255个字符！'
                }
            }
        });
    </script>
@endsection