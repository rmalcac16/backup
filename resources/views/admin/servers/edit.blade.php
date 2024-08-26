@extends('admin.layout')

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.servers.update',[$server->id]) }}" method="POST">
				@method('PUT')
				@csrf
			    <div class="form-group mb-4">
			        <label for="title">{{ __('Title') }}</label>
			        <input type="text" class="form-control" id="title" name="title" placeholder="{{ __('Title') }}" value="{{ $server->title }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="embed">{{ __('Embed') }}</label>
			        <input type="text" class="form-control" id="embed" name="embed" placeholder="{{ __('Embed') }}" value="{{ $server->embed }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="type">{{ __('Type') }}</label>
			        <select class="form-control" id="type" name="type" placeholder="{{ __('Type') }}">
			        	<option value="0"@if($server->type == 0) selected @endif>{{ __('Direct Link') }}</option>
			        	<option value="1"@if($server->type == 1) selected @endif>{{ __('Iframe') }}</option>
					<option value="2"@if($server->type == 2) selected @endif>{{ __('Generated') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="status">{{ __('Status') }}</label>
			        <select class="form-control" id="status" name="status" placeholder="{{ __('Status') }}">
						<option value="0"@if($server->status == 0) selected @endif>{{ __('Offline') }}</option>
			        	<option value="1"@if($server->status == 1) selected @endif>{{ __('Online') }}</option>
						<option value="2"@if($server->status == 2) selected @endif>{{ __('Only Web') }}</option>
						<option value="3"@if($server->status == 3) selected @endif>{{ __('Only App') }}</option>
			        </select>
			    </div>
			    <input type="submit" value="{{ __('Edit') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>
    
@endsection