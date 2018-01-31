@extends('header')

@section('title', '承包商公司明细')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">承包商公司明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('company/search') }}">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{ $_GET['company_name'] or '' }}" name="company_name" placeholder="公司名称">&nbsp;或者
                        <input type="text" class="form-control" value="{{ $_GET['person_name'] or '' }}" name="person_name"  placeholder="负责人/联系人">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>&nbsp;&nbsp;或&nbsp;
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('company/search') }}' + '?' + sParam;
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
    @foreach($companies as $company)
        <div class="company">
            <div class="title">
                <h3>
                    {{ $company->company_name }}
                </h3>
                <span class="company-description">{{ $company->company_description }}</span>
            </div>
            <div class="company-content">
                <div class="l">
                    <p>
                        <strong>日常联系人：</strong>
                        <span>{{ $company->linkman }}</span><br/>
                    </p>
                    <p>
                        <strong>联系人电话：</strong>
                        <span>{{ $company->linkman_tel }}</span>
                    </p>
                </div>
                <div class="r">
                    <p>
                        <strong>公司负责人：</strong>
                        <span>{{ $company->manager }}</span><br/>
                    </p>
                    <p>
                        <strong>负责人电话：</strong>
                        <span>{{ $company->manager_tel }}</span>
                    </p>
                </div>
                <div class="down">
                    <p><strong>入住时间：</strong><span>{{substr($company->created_at, 0, 10)}}</span></p>
                    <strong>所居住房间：</strong>
                    {{--<p class="all-rooms">
                        @if (isset($rooms[$company->company_id]))
                            @if (count($rooms[$company->company_id]) <= 8)
                                @foreach($rooms[$company->company_id] as $room)
                                    <a href="" class="company-room">{{ $room }}</a>
                                @endforeach
                            @else
                                @for($i=0; $i<8; $i++)
                                    <a href="" class="company-room">{{ $rooms[$company->company_id][$i] }}</a>
                                @endfor
                                <a href="" class="more">更多>></a>
                            @endif
                        @endif
                    </p>--}}
                    <p class="all-rooms">
                        @if ($company->rooms)
                            {{--房间多于8个--}}
                            @foreach($company->rooms as $key=>$room)
                                @if ($key < 8)
                                    <a href="javascript:;" class="company-room">{{ $room->room_name }}</a>
                                @endif
                            @endforeach
                            <a href="{{ url('company/company-detail/'.$company->company_id) }}" class="more" >详细>></a>
                        @endif
                    </p>
                    <strong>备注：</strong>
                    <p class="company-remark">{{ $company->company_remark }}</p>
                    <div class="func">
                        <a href="{{ url('punish/create/'.$company->company_id) }}" class="btn btn-danger btn-xs">处罚</a>
                        <a href="{{ url('company/change-rooms/'.$company->company_id) }}" class="btn btn-success btn-xs">调整房间</a>
                        <a href="{{ url('company/edit/'.$company->company_id) }}" class="btn btn-warning btn-xs">修改</a>
                        <a href="{{ url('company/quit/'.$company->company_id) }}" class="btn btn-danger btn-xs">退租</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- delete modal -->
    <div id="modal" class="modal bs-example-modal-sm">
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
                    <button id="modal-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('bottom')
    <p>共有 {{ $count['company'] }} 个公司</p>
    <p>共占用 {{ $count['livingRoom'] }} 个房间</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection