@extends('header')

@section('title', '公司详情')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/detail.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">{{ $company->company_name }} - 详情</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
            <table class="table table-hover table-condensed">
                <tr class="no-border">
                    <th>公司名称</th>
                    <td>
                        {{ $company->company_name }}
                    </td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td>
                        {{$company->company_description}}
                    </td>
                </tr>
                <tr>
                    <th>入住时间</th>
                    <td >
                        {{ substr($company->created_at, 0, 10) }}
                    </td>
                </tr>
                <tr>
                    <th>日常联系人</th>
                    <td>
                        {{ $company->linkman }}
                    </td>
                </tr>
                <tr>
                    <th>联系人电话</th>
                    <td>
                        {{ $company->linkman_tel }}
                    </td>
                </tr>
                <tr>
                    <th>公司负责人</th>
                    <td>
                        {{ $company->manager }}
                    </td>
                </tr>
                <tr>
                    <th>负责人电话</th>
                    <td>
                        {{ $company->manager_tel }}
                    </td>
                </tr>
                <tr>
                    <th>备注</th>
                    <td>
                        {{ $company->remark }}
                    </td>
                </tr>
                <tr>
                    <th width="10%">占用房间</th>
                    <td>
                        @foreach($company->detail as $typeId => $typeDetail)
                            <div class="detail">
                                <p class="type-name">{{$types[$typeId]}}</p>
                                @foreach($typeDetail as $personNumber => $rooms)
                                    <div class="rooms">
                                        <p class="person-number">
                                            @if ($personNumber > 0)
                                                {{$personNumber}} 人间 -
                                            @endif
                                            <span style="color:red">{{$company->count[$typeId][$personNumber]}}</span>套：
                                        </p>
                                        <p class="room-detail">
                                            {{$rooms}}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </td>
                </tr>
            </table>
            <div class="form-submit">
                <a href="{{ url('company/index') }}" class="btn btn-success" >返回</a>
            </div>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
@endsection