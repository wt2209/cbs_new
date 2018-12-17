<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>承包商公寓管理 - 日程)</title>
    <link rel="stylesheet" href="{{ asset('/bootstrap-3.3.5/css/bootstrap.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
    <style>
        #calendar {
            max-width: 900px;
            margin: 5px auto;
        }
    </style>
    <script src="{{ asset('js/jquery-1.11.3.js') }}"></script>
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row" style="margin-bottom: 20px;">
        <h2 style="text-align: center;font-size: 48px;">欢迎使用</h2>
        {{--<h1 style="text-align: center;font-size: 54px;">承包商公寓管理系统</h1>--}}
    </div>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-4 col-xs-4">
            <div class="info-box info-box-warning"  style="min-height: 96px;">
                <div class="info-box-content">
                    <span class="info-box-text">当前公司总数</span>
                    <br>
                    <span class="info-box-text">{{$company_total_count}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-4">
            <div class="info-box info-box-warning" style="min-height: 96px;">
                <div class="info-box-content">
                    <span class="info-box-text">已用房间总数</span>
                    <br>
                    <span class="info-box-text">{{$company_total_count}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-4">
            <div class="info-box info-box-warning"  style="min-height: 96px;">
                <div class="info-box-content">
                    <span class="info-box-text">本月已维修量</span>
                    <br>
                    <span class="info-box-text">{{$repair_current_count}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
            @foreach($detail as $typeName => $type)
            <div class="col-md-4 col-xs-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{$typeName}}</h3>
                    </div>
                    <div class="panel-body" style="padding:0;">
                        @foreach($type as $building => $info)
                            <div class="col-md-6">
                                <div class="info-box info-box-primary">
                                    <div class="info-box-content" style="margin:0">
                                        <span class="info-box-text">{{$building}}</span>
                                        <span class="info-box-number">当前公司数：{{$info['company_count']}}</span>
                                        <span class="info-box-number">已用房间数：{{$info['room_count']}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="panel-footer">
                        <p style="margin: 0;">
                            当前公司总数：{{array_sum(array_column($type, 'company_count'))}}，
                            已用房间总数：{{array_sum(array_column($type, 'room_count'))}}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
    </div>
</div>
</body>
</html>




