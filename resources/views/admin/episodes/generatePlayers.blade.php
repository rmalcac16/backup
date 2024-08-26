@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.episodes.players.storePlayers',[$anime->id]) }}" method="POST">
				@csrf
				<div class="form-group mb-4">
			        <label for="quantity">{{ __('Quantity episodes') }}</label>
			        <input disabled type="number" class="form-control" id="quantity" name="quantity" placeholder="{{ __('Quantity episodes') }}" value={{ count($episodes) }}>
			    </div>
			    @if(count($episodes) > 0)
			    <div class="form-group mb-4">
			        <label for="first">{{ __('First episode') }}</label>
			        <input type="number" class="form-control" id="first" name="first" placeholder="{{ __('First episode') }}" value={{ old('first') }}>
			    </div>
			    <div class="form-group mb-4">
			        <label for="last">{{ __('Last episode') }}</label>
			        <input type="number" class="form-control" id="last" name="last" placeholder="{{ __('Last episode') }}" value={{ old('last') }}>
			    </div>
			    <div class="form-group mb-4">
			        <label for="server_id">{{ __('Server') }}</label>
			        <select class="form-control mb-4" id="server_id" name="server_id">
			        	@foreach ($servers as $server)
			        		<option value="{{ $server->id }}">{{ $server->title }}</option>
			        	@endforeach
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="languaje">{{ __('Languaje') }}</label>
			        <select class="form-control mb-4" id="languaje" name="languaje">
			        	<option value="0">{{ __('Subbed') }}</option>
			        	<option value="1">{{ __('Latino') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="list">{{ __('List of players') }}</label>
			        <textarea class="form-control" id="list" name="list" rows="6" placeholder="{{ __('List of players') }}"></textarea>
			    </div>
			    <input type="submit" value="{{ __('Generate players') }}" class="btn btn-primary">
			    @else
			    <div class="alert alert-danger mb-4" role="alert">
    				<strong>{{ __('Error!') }}</strong> {{ __('You must add at least one episode.') }}
				</div>
			    @endif
			</form>
		</div>
	</div>
</div>
    
@endsection