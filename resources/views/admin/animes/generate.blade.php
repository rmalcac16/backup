@extends('admin.layout')

@section('styles')
<style type="text/css">
.generado {
  position: relative;
}

.generado .year {
  position: absolute;
    top: 5px;
    left: 5px;
    padding: 2px 3px;
    background: red;
    color: #fff;
    font-size: 10px;
    border-radius: 0 0 4px 0;
}

.generado .add{
    position: absolute;
    right: 0;
    padding: 5px 10px;
    background: #ffe100;
    color: #000;
    font-size: 10px;
    border-radius: 0 0 0 4px;
}

.title {
    position: absolute;
    bottom: 0;
    padding: 5px;
    width: 100%;
}
.title p{
    background: #000000b3;
    color: #fff;
    padding: 5px;
    margin: 0;
}

</style>
@endsection

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
		<div class="widget-content-area">
		    <div class="form-group mb-4">
		        <label for="search">{{ __('Title') }}</label>
		        <input type="text" class="form-control" id="search" placeholder="{{ __('Title') }}" value="{{ old('name') }}">
		    </div>
		    <div id="results" class="row px-3"></div>
		</div>
	</div>
</div>
    
@endsection

@section('aditionals')
<script type="text/javascript">
	$("#search").on("change paste keyup", function() {
		fetch('https://api.themoviedb.org/3/search/tv?api_key=96821ae32edefecbd2270c4b46a61db0&language=es-MX&include_adult=true&query='+$(this).val())
	  	.then(response => response.json())
	  	.then(data => {
	  		var items = '';
	  		$.each(data.results, function(key,value){
				items += '<div class="col-6 col-sm-4 col-md-3 col-lg-2 p-1"><form method="POST" action="{{ route('admin.animes.store') }}">@csrf';
				items +='<input style="display:none" name="id" value="'+value?.id+'">';
				items +='<input style="display:none" name="name" value="'+value?.name+'">';
				items +='<input style="display:none" name="name_alternative" value="'+value?.original_name+'">';
				items +='<input style="display:none" name="banner" value="'+value?.backdrop_path+'">';
				items +='<input style="display:none" name="poster" value="'+value?.poster_path+'">';
				items +='<input style="display:none" name="overview" value="'+value?.overview+'">';
				items +='<input style="display:none" name="aired" value="'+value?.first_air_date+'">';
				items +='<input style="display:none" name="type" value="Tv">';
				items +='<input style="display:none" name="status" value="0">';
				items +='<input style="display:none" name="premiered" value="">';
				items +='<input style="display:none" name="broadcast" value="">';
				items +='<input style="display:none" name="rating" value="">';
				items +='<input style="display:none" name="popularity" value="'+value?.popularity+'">';
				items +='<input style="display:none" name="vote_average" value="'+value?.vote_average+'">';
				items +='<div class="card generado"><div class="year">'+value?.first_air_date?.slice(0,4)+'</div><input type="submit" class="btn add" value="+" name="generar"><img class="w-100 img-thumbnail" src="http://image.tmdb.org/t/p/w154'+value?.poster_path+'"><div class="title"><p class="text-truncate m-0">'+value?.name+'</p></div></div></form></div>'
			});
			$("#results").html(items);
		})
	});
</script>
@endsection