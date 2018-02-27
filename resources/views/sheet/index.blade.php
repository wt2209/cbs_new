@extends('header')

@section('title', '生成报表')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/room/edit.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">生成报表</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('record/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="navbar-header">
            <form class="navbar-form navbar-left" role="search">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="form-group">
                    生成 
                    <input type="text" name="year" class="form-control" placeholder="年">
                    <input type="text" name="month" class="form-control" placeholder="月">
                     的报表
                </div>
                <button id="submit" class="btn btn-success">生成</button>
                <script>
                    $('#submit').click(function(){
                        var sParam = $('form').serialize();
                        var sUrl = '{{ url('sheet/create') }}' + '?' + sParam;
                        maskShow();
                        window.location = sUrl;
                        setTimeout(maskHide,2000);
                        return false;
                    })
                </script>
            </form>
        </div>
    </div>
@endsection

@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
@endsection