@extends('admin.layout')

@section("styles")
<link href="{{ asset('plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.store') }}" method="POST">
				@csrf
			    <div class="form-group mb-4">
			        <label for="title">{{ __('Title') }}</label>
			        <input type="text" class="form-control" id="title" name="name" placeholder="{{ __('Title') }}" value="{{ old('title') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="title_original">{{ __('Title original') }}</label>
			        <input type="text" class="form-control" id="title_original" name="name_alternative" placeholder="{{ __('Title original') }}" value="{{ old('title_original') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="type">{{ __('Type') }}</label>
			        <select class="form-control" id="type" name="type" title="{{ __('Select type') }}">
			        	<option value="TV">{{ __('Anime') }}</option>
			        	<option value="Movie">{{ __('Movie') }}</option>
			        	<option value="Special">{{ __('Special') }}</option>
			        	<option value="OVA">{{ __('Ova') }}</option>
			        	<option value="ONA">{{ __('Ona') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="status">{{ __('Status') }}</label>
			        <select class="form-control" id="status" name="status" title="{{ __('Select status') }}">
			        	<option value="1">{{ __('Currently Airing') }}</option>
			        	<option value="0">{{ __('Finished Airing') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="banner">{{ __('Backdrop') }}</label>
			        <input type="text" class="form-control" id="banner" name="banner" placeholder="{{ __('Backdrop') }}" value="{{ old('banner') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="poster">{{ __('Poster') }}</label>
			        <input type="text" class="form-control" id="poster" name="poster" placeholder="{{ __('Poster') }}" value="{{ old('poster') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="aired">{{ __('Aired') }}</label>
			        <input type="date" class="form-control" id="aired" name="aired" placeholder="{{ __('Aired') }}" value="{{ old('aired') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="overview">{{ __('Overview') }}</label>
			        <textarea class="form-control" id="overview" name="overview" rows="4">{{ old('overview') }}</textarea>
			    </div>
				<div class="form-group mb-4">
			        <label for="premiered">{{ __('Premiered') }}</label>
			        <input type="text" class="form-control" id="premiered" name="premiered" placeholder="{{ __('Premiered') }}" value="{{ old('premiered') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="broadcast">{{ __('Broadcast') }}</label>
					<select class="form-control" id="broadcast" name="broadcast" title="{{ __('Select broadcast') }}">
						<option value="1">{{ __('Monday') }}</option>
						<option value="2">{{ __('Tuesday') }}</option>
			        	<option value="3">{{ __('Wednesday') }}</option>
						<option value="4">{{ __('Thursday') }}</option>
						<option value="5">{{ __('Friday') }}</option>
						<option value="6">{{ __('Saturday') }}</option>
						<option value="7">{{ __('Sunday') }}</option>
			        </select>
			    </div>
				<div class="form-group mb-4">
			        <label for="genres">{{ __('Genres') }}</label>
			    	<select id="genres" class="form-control genres" multiple="multiple" name="genres[]">
						@forelse($genres as $genre)
						<option value="{{ $genre->slug }}">{{ $genre->title }}</option>
						@empty
						@endforelse
					</select>
				</div>
			    <div class="form-group mb-4">
			        <label for="rating">{{ __('Rating') }}</label>
			        <input type="text" class="form-control" id="rating" name="rating" placeholder="{{ __('Rating') }}" value="{{ old('rating') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="popularity">{{ __('Popularity') }}</label>
			        <input min="0" type="number" class="form-control" id="popularity" name="popularity" placeholder="{{ __('Popularity') }}" value="{{ old('popularity') }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="vote_average">{{ __('Vote average') }}</label>
			        <input min="0" max="10" type="double" class="form-control" id="vote_average" name="vote_average" placeholder="{{ __('Vote average') }}" value="{{ old('vote_average') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="trailer">{{ __('Trailer') }}</label>
			        <input min="0" max="10" type="double" class="form-control" id="trailer" name="trailer" placeholder="{{ __('Trailer ID YOUTUBE') }}" value="{{ old('trailer') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_flv">{{ __('Slug AnimeFLV') }}</label>
			        <input type="text" class="form-control" id="slug_flv" name="slug_flv" placeholder="{{ __('Slug AnimeFLV') }}" value="{{ old('slug_flv') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_tio">{{ __('Slug TioAnime') }}</label>
			        <input type="text" class="form-control" id="slug_tio" name="slug_tio" placeholder="{{ __('Slug TioAnime') }}" value="{{ old('slug_tio') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_jk">{{ __('Slug JkAnime') }}</label>
			        <input type="text" class="form-control" id="slug_jk" name="slug_jk" placeholder="{{ __('Slug JkAnime') }}" value="{{ old('slug_jk') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_monos">{{ __('Slug MonosChinos') }}</label>
			        <input type="text" class="form-control" id="slug_monos" name="slug_monos" placeholder="{{ __('Slug MonosChinos') }}" value="{{ old('slug_monos') }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_fenix">{{ __('Slug AnimeFenix') }}</label>
			        <input type="text" class="form-control" id="slug_fenix" name="slug_fenix" placeholder="{{ __('Slug AnimeFenix') }}" value="{{ old('slug_fenix') }}">
			    </div>
			    <input type="submit" value="{{ __('Add') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>   
@endsection

@section("aditionals")
<script src="{{ asset('plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
<script>
$(".genres").select2({
	tags: true,
	placeholder: "Seleccionar generos"
});
</script>
@endsection