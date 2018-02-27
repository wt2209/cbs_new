@extends('header')

@section('title', '承包商公司历史记录')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">承包商公司历史记录</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/history') }}" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>公司名</th>
                <th>日常联系人</th>
                <th>联系人电话</th>
                <th>公司负责人</th>
                <th>负责人电话</th>
                <th>公司状态</th>
                <th>入住时间</th>
                <th>退房时间</th>
                <th>备注</th>
            </tr>
            </thead>
            @foreach($companies as $company)
                <tr>
                    <td>
                        {{ $company->company_name }}
                    </td>
                    <td>{{ $company->linkman }}</td>
                    <td>{{ $company->linkman_tel }}</td>
                    <td>{{ $company->manager }}</td>
                    <td>{{ $company->manager_tel }}</td>
                    <td>{{ $company->is_quit == 0 ? '正常' : '已退租' }}</td>
                    <td>{{ $company->created_at }}</td>
                    <td>{{ $company->quit_at }}</td>
                    <td>{{ $company->company_remark }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
@section('bottom')
    <p>共有 {{ count($companies) }} 个公司</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection