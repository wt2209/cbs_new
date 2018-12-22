@extends('header')
@section('title', '未审核项目')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">未审核项目</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    {{--    <div class="function-area">

        </div>--}}
@endsection
@section('content')
    <div class="table-responsive">
        <div class="function-group">
            <button class="btn btn-success" id="patch-review">批量审核通过</button>
        </div>
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th style="text-align: center; width:40px;">
                    <label>
                    <input type="checkbox" id="review-items-parent">
                    </label>
                </th>
                <th>位置/房间号</th>
                <th>报修内容</th>
                <th>报修人</th>
                <th>报修时间</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach ($underReviews as $underReview)
                {{--正在使用--}}
                <tr>
                    <td style="text-align: center;">
                        <label>
                            <input type="checkbox" data-id="{{$underReview->id}}" class="review-items">
                        </label>
                    </td>
                    <td>{{ $underReview->location }}</td>
                    <td>{{ $underReview->content }}</td>
                    <td>{{ $underReview->name }}</td>
                    <td>{{ $underReview->report_at }}</td>
                    <td>
                        <a href="{{ url('repair/review/'.$underReview->id) }}" class="btn btn-success btn-xs">审核</a>
                        <a href="{{ url('repair/edit/'.$underReview->id) }}" class="btn btn-warning btn-xs">修改</a>
                        {{--<a href="javascript:;" delete_id="{{ $room->room_id }}" class="btn btn-danger btn-xs delete-button">删除</a>--}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
@section('modal')

@endsection
@section('bottom')
    <p>共有 {{count($underReviews)}} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        $(function () {
            // 全选 反选
            $('#review-items-parent').click(function () {
                if (this.checked) {
                    $(".review-items").prop("checked", true);
                } else {
                    $(".review-items").prop("checked", false);
                }
            })

            // 批量审核
            $('#patch-review').click(function () {
                ids = [];
                $('.review-items').each(function () {
                    if (this.checked) {
                        ids.push(this.dataset.id);
                    }
                })

                maskShow();
                $.post('{{url('repair/patch-review')}}', {ids}, function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                location.reload(true);
                            }
                        }});
                }, 'json');
            })
        })
    </script>
@endsection