@extends('front/global')

@section('content')
    @if (Auth::check() && Auth::user()->user_type == 'employer')
        <div style="padding-left:232px; padding-bottom: 400px;" class="content_dangky mt16" >
    @else
        <div class="content_dangky  mt16">
    @endif

    @include('front/home/ads')

    @include('front/home/count',['countData' => $countData])

    <div class="box_tab bg_white box_shadow pt_16 pl_14 pr_14 pb_16">
        <div class="w_100">
            <div class="box_info text-center">
                <div class="list-items mb_4">
                    <span class="text_pink font16">BẠN ĐÃ TẠO HỒ SƠ TRỰC TUYẾN THÀNH CÔNG</span>
                </div>
                <div class="list-items mb_4">
                    Hồ sơ của bạn sẽ được Kiểm duyệt & Hiển thị tại Ngân Hàng Ứng Viên trong vòng 24h!</b>
                    <br>
                    Chúc bạn sớm tìm được công việc phù hợp!
                </div>
            </div>
        </div>
    </div>
    <div class="form-group clearfix mb_16"></div>
    @include('front/home/contact_info')
    </div>
@endsection