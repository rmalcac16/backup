@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.genres.update',[$genre->id]) }}" method="POST">
				@method('PUT')
				@csrf
			    <div class="form-group mb-4">
			        <label for="title">{{ __('Title') }}</label>
			        <input type="text" class="form-control" id="title" name="title" placeholder="{{ __('Title') }}" value="{{ $genre->title }}">
			    </div>
			    <input type="submit" value="{{ __('Edit') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>
    
@endsection