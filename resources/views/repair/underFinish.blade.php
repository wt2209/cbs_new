@extends('header')
@section('title', '未完工项目')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/index.css') }}"/>
    <style>
        .border-black, .border-black tr, .border-black td, .border-black th{
            text-align: center;
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
    </style>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">未完工项目</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>

@endsection
@section('content')
    <div class="table-responsive">
        <div class="function-group">
            <button class="btn btn-success" id="patch-finish">批量完工</button>
        </div>
        <table id="table" class="table table-bordered table-hover table-condensed" print-url="{{ url('repair/print/') }}">
            <thead>
            <tr class="hidden" id="print-title">
                <td class="no-print"  style="width: 40px;"></td>
                <td>位置/房间号</td>
                <td>报修内容</td>
                <td>报修人</td>
                <td>报修时间</td>
                <td class="no-print">审核人</td>
                <td class="no-print">审核时间</td>
                <td>审核说明</td>
                <td class="no-print">首次打印时间</td>
                <td class="no-print">操作</td>
            </tr>
            <tr class="active" id="display-title">
                <th class="no-print" style="width: 40px;text-align: center;">
                    <label>
                        <input type="checkbox" id="finish-items-parent">
                    </label>
                </th>
                <th>位置/房间号</th>
                <th>报修内容</th>
                <th>报修人</th>
                <th>报修时间</th>
                <th class="no-print">审核人</th>
                <th class="no-print">审核时间</th>
                <th>审核说明</th>
                <th class="no-print">首次打印时间</th>
                <th class="no-print">操作</th>
            </tr>
            </thead>
            @foreach ($items as $item)
                {{--正在使用--}}
                <tr id="print-{{$item->id}}" class="no-print">
                    <td style="text-align: center;" class="no-print">
                        <label>
                            <input type="checkbox" data-id="{{$item->id}}" class="finish-items">
                        </label>
                    </td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->content }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->report_at }}</td>
                    <td class="no-print">{{ $item->reviewer }}</td>
                    <td class="no-print">{{ $item->reviewed_at }}</td>
                    <td>{{ $item->review_remark }}</td>
                    <td class="no-print">{{ $item->printed_at == '0000-00-00 00:00:00' ?'':  $item->printed_at}}</td>
                    <td class="no-print">
                        <a href="javascript:;" _id="{{$item->id}}" class="print-button btn btn-success btn-xs">打印</a>
                        <a href="{{ url('repair/finish/'.$item->id) }}" class="btn btn-warning btn-xs">完工</a>
                        <a href="javascript:;" delete_id="{{ $item->id }}" class="btn btn-danger btn-xs delete-button">取消</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
@section('modal')
    <!-- delete modal -->
    <div id="delete-modal" class="modal fade bs-example-modal-sm">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">取消确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要取消吗？
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
    <p>共有 {{count($items)}} 条记录</p>
@endsection
@section('js')
    <script src="{{ asset('/js/jquery.print.min.js') }}"></script>
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        ajaxDelete('{{ url('repair/cancel') }}')

        $(function(){
            // 全选 反选
            $('#finish-items-parent').click(function () {
                if (this.checked) {
                    $(".finish-items").prop("checked", true);
                } else {
                    $(".finish-items").prop("checked", false);
                }
            });

            // 批量完工
            $('#patch-finish').click(function () {
                ids = [];
                $('.finish-items').each(function () {
                    if (this.checked) {
                        ids.push(this.dataset.id);
                    }
                })
                console.log(ids);

                maskShow();
                $.post('{{url('repair/patch-finish')}}', {ids}, function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                            if (e.status) {
                                location.reload(true);
                            }
                        }});
                }, 'json');
            })


            //打印
            $('.print-button').click(function(){
                var displayTitle = $('#display-title');
                var printTitle = $('#print-title');
                var id = $(this).attr('_id');
                var currentRow = $("#print-"+id);
                var table = $("#table");
                var url = table.attr('print-url');
                var myDate = new Date();
                var currentTime = myDate.getFullYear() + '-' +(myDate.getMonth()+1) + '-' + myDate.getDate()
                    + ' '+myDate.getHours() + ':' + myDate.getMinutes();

                var str = '';
                table.addClass('border-black');
                table.removeClass('table-bordered');
                printTitle.removeClass('hidden');
                displayTitle.addClass('hidden');

                currentRow.removeClass('no-print');
                table.print({
                    //Use Global styles
                    globalStyles : true,
                    //Add link with attrbute media=print
                    mediaPrint : false,
                    //Print in a hidden iframe
                    iframe : false,
                    //Don't print this
                    noPrintSelector : ".no-print",
                    //Add this at top
                    prepend : "<p style='font-size: 16px;font-weight: bold;text-align: center;'>维修申请单"+ str +"</p><br/>",
                    //Add this on bottom
                    append : "<div class='pull-right' style='width:300px;'>打印时间： "+currentTime+"<br><br>维修完工签字：</div> "
                });
                currentRow.addClass('no-print');
                printTitle.addClass('hidden');
                displayTitle.removeClass('hidden');
                table.removeClass('border-black');
                table.addClass('table-bordered');

                $.get(url, 'id='+id, function(){
                    location.reload()
                    window.parent.getRepairNotify();
                }, 'json');
            });
        })
    </script>
@endsection