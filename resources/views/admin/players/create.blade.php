@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.episodes.players.store',[$anime->id,$episode->id]) }}" method="POST">
				@csrf
				<div class="form-group mb-4">
			        <label for="server_id">{{ __('Server') }}</label>
			        <select class="form-control" id="server_id" name="server_id" placeholder="{{ __('Server') }}">
			        	@forelse($servers as $server)
			        	<option value="{{ $server->id }}"@if(old('server') == $server->id) selected @endif>{{ $server->title }}</option>
			        	@empty
			        	@endforelse
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="code">{{ __('Code') }}</label>
			        <input type="text" class="form-control" id="code" name="code" placeholder="{{ __('Code') }}" value={{ old('code') }}>
			    </div>
			    <div class="form-group mb-4">
			        <label for="languaje">{{ __('Languaje') }}</label>
			        <select class="form-control" id="languaje" name="languaje" placeholder="{{ __('Languaje') }}">
			        	<option value="0"@if(old('languaje') == 0) selected @endif>{{ __('Subbed') }}</option>
			        	<option value="1"@if(old('languaje') == 1) selected @endif>{{ __('Latino') }}</option>
			        </select>
			    </div>
			    <input type="submit" value="{{ __('Add') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>
    
@endsection