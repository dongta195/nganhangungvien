@endsection
@extends('global_admin')
<title>@lang('messages.site.title')</title>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject bold uppercase"> {{$pageTitle}}</span>
                    </div>
                    <div class="actions">
                        <div class="btn-group">
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
                        <thead>
                            <tr>
                                <th style="width: 10px;">STT</th>
                                <th style="width: 85%;" class="text-center">Số năm kinh nghiệm</th>
                                <th style="width: 61px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($experienceyearsList) > 0)
                                @foreach($experienceyearsList as $index=>$item)
                                    <tr class="gradeX odd" role="row">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $item->name }}</td>
                                        <td>
                                            <a class="sweet-delete"
                                                data-id="{{$item->id}}"
                                                data-url="{{route('admin.experienceyears.delete', ['id' => $item->id])}}">
                                                <button type="button" class="btn btn-icon-toggle " data-toggle="tooltip"
                                                        data-placement="top" data-original-title="Delete row"><i class="fa fa-trash-o"></i></button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                Chưa có thông tin phù hợp
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@endsection