@extends('front/global')

<title>@lang('messages.site.title')</title>
@section('content')
<style>
	.title {
		font-size: 18px;
		margin: 40px 0px;
	}
	.pt_6 .pb_24{
		min-height: 600px;
	}
</style>
<div class="text-center">
	<div class="title" >@lang('messages.error.404', ['homepage' => route('index')])</div>
</div>
@endsection