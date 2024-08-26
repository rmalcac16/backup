@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.episodes.store',[$anime->id]) }}" method="POST">
				@csrf
			    <div class="form-group mb-4">
			        <label for="number">{{ __('Number') }}</label>
			        <input type="number" class="form-control" id="number" name="number" placeholder="{{ __('Number') }}" value={{ old('number') }}>
			    </div>
			    <input type="submit" value="{{ __('Add') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>
    
@endsection