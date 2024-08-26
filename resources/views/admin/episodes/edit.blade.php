@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.episodes.update',[$anime->id,$episode->id]) }}" method="POST">
				@method('PUT')
				@csrf
			    <div class="form-group mb-4">
			        <label for="number">{{ __('Number') }}</label>
			        <input type="number" class="form-control" id="number" name="number" placeholder="{{ __('Number') }}" value={{ $episode->number }}>
			    </div>
			    <input type="submit" value="{{ __('Edit') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>
    
@endsection