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

.name {
    position: absolute;
    bottom: 0;
    padding: 5px;
    width: 100%;
}
.name p{
    background: #000000b3;
    color: #fff;
    padding: 5px;
    margin: 0;
}

</style>
<link href="{{ asset('plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
@include('admin.inc.flash-message')
<div class="row layout-spacing">
	<div class="col-lg-12">
	    <a href="#">
			<button type="button" class="btn btn-primary mb-2 mr-2" data-toggle="modal" data-target="#exampleModal">{{ __('Generate Data') }}</button>
		</a>
		<div class="widget-content-area">
			<form accept="UTF-8" action="{{ route('admin.animes.update',[$anime->id]) }}" method="POST">
				@method('PUT')
				@csrf
			    <div class="form-group mb-4">
			        <label for="name">{{ __('Title') }}</label>
			        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Title') }}" value="{{ $anime->name }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug">{{ __('Slug') }}</label>
			        <input type="text" class="form-control" id="slug" name="slug" placeholder="{{ __('Slug') }}" value="{{ $anime->slug }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="name_alternative">{{ __('Title original') }}</label>
			        <input type="text" class="form-control" id="name_alternative" name="name_alternative" placeholder="{{ __('Title original') }}" value="{{ $anime->name_alternative }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="type">{{ __('Type') }}</label>
			        <select class="form-control" id="type" name="type" placeholder="{{ __('Type') }}">
			        	<option value="TV"@if($anime->type == 'TV') selected @endif>{{ __('Anime') }}</option>
			        	<option value="Movie"@if($anime->type == 'Movie') selected @endif>{{ __('Movie') }}</option>
			        	<option value="Special"@if($anime->type == 'Special') selected @endif>{{ __('Special') }}</option>
			        	<option value="OVA"@if($anime->type == 'OVA') selected @endif>{{ __('Ova') }}</option>
			        	<option value="ONA"@if($anime->type == 'ONA') selected @endif>{{ __('Ona') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="status">{{ __('Status') }}</label>
			        <select class="form-control" id="status" name="status" placeholder="{{ __('Status') }}">
			        	<option value="1"@if($anime->status == 1) selected @endif>{{ __('Currently Airing') }}</option>
			        	<option value="0"@if($anime->status == 0) selected @endif>{{ __('Finished Airing') }}</option>
			        </select>
			    </div>
			    <div class="form-group mb-4">
			        <label for="banner">{{ __('Backdrop') }}</label>
			        <input type="text" class="form-control" id="banner" name="banner" placeholder="{{ __('Backdrop') }}" value="{{ $anime->banner }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="poster">{{ __('Poster') }}</label>
			        <input type="text" class="form-control" id="poster" name="poster" placeholder="{{ __('Poster') }}" value="{{ $anime->poster }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="aired">{{ __('Aired') }}</label>
			        <input type="date" class="form-control" id="aired" name="aired" placeholder="{{ __('Aired') }}" value="{{ $anime->aired ? $anime->aired->format('Y-m-d') : '' }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="overview">{{ __('Overview') }}</label>
			        <textarea class="form-control" id="overview" name="overview" rows="4">{{ $anime->overview }}</textarea>
			    </div>
				<div class="form-group mb-4">
			        <label for="premiered">{{ __('Premiered') }}</label>
			        <input type="text" class="form-control" id="premiered" name="premiered" placeholder="{{ __('Premiered') }}" value="{{ $anime->premiered }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="broadcast">{{ __('Broadcast') }}</label>
					<select class="form-control" id="broadcast" name="broadcast" name="{{ __('Select broadcast') }}">
						<option value="1"@if($anime->broadcast == 1) selected @endif>{{ __('Monday') }}</option>
						<option value="2"@if($anime->broadcast == 2) selected @endif>{{ __('Tuesday') }}</option>
			        	<option value="3"@if($anime->broadcast == 3) selected @endif>{{ __('Wednesday') }}</option>
						<option value="4"@if($anime->broadcast == 4) selected @endif>{{ __('Thursday') }}</option>
						<option value="5"@if($anime->broadcast == 5) selected @endif>{{ __('Friday') }}</option>
						<option value="6"@if($anime->broadcast == 6) selected @endif>{{ __('Saturday') }}</option>
						<option value="7"@if($anime->broadcast == 7) selected @endif>{{ __('Sunday') }}</option>
			        </select>
			    </div>
				<div class="form-group mb-4">
			        <label for="genres">{{ __('Genres') }}</label>
			    	<select id="genres" class="form-control genres" multiple="multiple" name="genres[]">
						@forelse($genres as $genre)
						<option value="{{ $genre->slug }}" @forelse(explode(",",$anime->genres) as $genr) @if($genr == $genre->slug) selected @endif @empty @endforelse>{{ $genre->title }}</option>
						@empty
						@endforelse
					</select>
				</div>
			    <div class="form-group mb-4">
			        <label for="rating">{{ __('Rating') }}</label>
			        <input type="text" class="form-control" id="rating" name="rating" placeholder="{{ __('Rating') }}" value="{{ $anime->rating }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="popularity">{{ __('Popularity') }}</label>
			        <input min="0" type="number" class="form-control" id="popularity" name="popularity" placeholder="{{ __('Popularity') }}" value="{{ $anime->popularity }}">
			    </div>
			    <div class="form-group mb-4">
			        <label for="vote_average">{{ __('Vote average') }}</label>
			        <input min="0" max="10" type="double" class="form-control" id="vote_average" name="vote_average" placeholder="{{ __('Vote average') }}" value="{{ $anime->vote_average }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="trailer">{{ __('Trailer') }}</label>
			        <input min="0" max="10" type="double" class="form-control" id="trailer" name="trailer" placeholder="{{ __('Trailer ID YOUTUBE') }}" value="{{ $anime->trailer }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_flv">{{ __('Slug AnimeFLV') }}</label>
			        <input type="text" class="form-control" id="slug_flv" name="slug_flv" placeholder="{{ __('Slug AnimeFLV') }}" value="{{ $anime->slug_flv }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_tio">{{ __('Slug TioAnime') }}</label>
			        <input type="text" class="form-control" id="slug_tio" name="slug_tio" placeholder="{{ __('Slug TioAnime') }}" value="{{ $anime->slug_tio }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_jk">{{ __('Slug JkAnime') }}</label>
			        <input type="text" class="form-control" id="slug_jk" name="slug_jk" placeholder="{{ __('Slug JkAnime') }}" value="{{ $anime->slug_jk }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_monos">{{ __('Slug MonosChinos') }}</label>
			        <input type="text" class="form-control" id="slug_monos" name="slug_monos" placeholder="{{ __('Slug MonosChinos') }}" value="{{ $anime->slug_monos }}">
			    </div>
				<div class="form-group mb-4">
			        <label for="slug_fenix">{{ __('Slug AnimeFenix') }}</label>
			        <input type="text" class="form-control" id="slug_fenix" name="slug_fenix" placeholder="{{ __('Slug AnimeFenix') }}" value="{{ $anime->slug_fenix }}">
			    </div>
			    <input type="submit" value="{{ __('Edit') }}" class="btn btn-primary">
			</form>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body row" id="results"> </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('aditionals')
<script src="{{ asset('plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
<script>
$(".genres").select2({
	tags: true,
	placeholder: "Seleccionar generos"
});
</script>
<script type="text/javascript">
    let ratingDB=[[["G - All Ages","Todas las edades"]],[["PG - Children","Niños"]],[["PG-13 - Teens 13 or older","Adolescentes de 13 años o más"]],[["R - 17+ (violence & profanity)","Adolescentes de 17 años o más (Violencia)"]],[["R+ - Mild Nudity","Solo adultos (Desnudos)"]],[["Rx - Hentai","No se permiten niños"]]];
    let genresDB=[[["Action","accion"]],[["Adventure","aventura"]],[["Comedy","comedia"]],[["Detective","detectives"]],[["Drama","drama"]],[["Ecchi","ecchi"]],[["Fantasy","fantasia"]],[["Gore","gore"]],[["Harem","harem"]],[["Historical","historico"]],[["Horror","horror"]],[["Isekai","isekai"]],[["Josei","josei"]],[["Mahou Shoujo","mahou-shoujo"]],[["Martial Arts","artes-marciales"]],[["Mecha","mecha"]],[["Military","militar"]],[["Mythology","mitologico"]],[["Music","musica"]],[["Mystery","misterio"]],[["Parody","parodia"]],[["Psychological","psicologico"]],[["Romance","romance"]],[["Romantic Subtext","romance"]],[["Samurai","samurais"]],[["School","escolar"]],[["Sci-Fi","ciencia-ficcion"]],[["Seinen","seinen"]],[["Shoujo","shoujo"]],[["Girls Love","shoujo-ai"]],[["Shounen","shounen"]],[["Boys Love","shounen-ai"]],[["Slice of Life","recuentos-de-la-vida"]],[["Space","espacio"]],[["Sports","deportes"]],[["Super Power","super-poderes"]],[["Supernatural","sobrenatural"]],[["Suspense","suspenso"]],[["Vampire","vampiros"]],[["Video Game","juegos"]]];
    let name = '{{ $anime->name }}';
	fetch('https://api.jikan.moe/v4/anime?q='+name)
  	.then(response => response.json())
  	.then(data => {
  		var items = '';
  		$.each(data.data, function(key,value){
			items += '<div class="col-12 col-sm-6 p-1"><a onclick="generateData(\'' + value.mal_id + '\')">';
			items += '<div class="card generado"><div class="year">'+value?.aired?.from?.slice(0,4)+'</div><input type="submit" class="btn add" value="'+value?.type+'" name="generar"><img class="w-100 img-thumbnail" src="'+value?.images?.jpg?.image_url+'"><div class="name"><p class="text-truncate m-0">'+value?.title+'</p></div></div></a></div>'
  		});
		$("#results").html(items);
	})
	function generateData(id) {
        fetch('https://api.jikan.moe/v4/anime/'+id+'/full')
      	.then(response => response.json())
      	.then(data => {

			//Titulo Anime
			const name = data?.data?.title;
			document.getElementById("name").value = name;
			
			//Titulos Alternativos Anime
			let titles = [data?.data?.title_japanese, data?.data?.title_english, ...data?.data?.title_synonyms];
			titles = titles.filter(function(el) {
				return el != null;
			});
			document.getElementById("name_alternative").value = titles;

			//Tipo Anime
			const type = data?.data?.type;
			document.getElementById("type").value = type;

			//Estado Anime
			const status = data?.data?.airing === true ? 1 : 0;
			document.getElementById("status").value = status;

			//Estreno Anime
			const aired = data?.data?.aired?.from?.slice(0,10);
			document.getElementById("aired").value = aired;

			//Temporada Anime
			const str = data?.data?.season+" "+data?.data?.year;
			const premiered = str.charAt(0).toUpperCase() + str.slice(1);
			document.getElementById("premiered").value = premiered;

			//Dia de Estreno Anime              ***Sin Funcionar***
			// var days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
			// let stringDate = data?.data?.broadcast?.string;
			// let DateFormat = '';
			// let dayNameDate = '';
			// let HourDate = '';
			// if(stringDate){
			// 	DateFormat = stringDate.split(" at ");
			// 	dayNameDate = DateFormat[0];
			// 	dayNameDate = dayNameDate.substring(0, dayNameDate.length - 1);
			// 	HourDate = DateFormat[1].split(' (JST)');
			// 	HourDate = HourDate[0].split(':');
			// 	HourDate = HourDate[0];
			// 	if(HourDate > 14){
			// 		document.getElementById("broadcast").selectedIndex = days.indexOf(dayNameDate);
			// 	}else{
			// 		document.getElementById("broadcast").selectedIndex = (days.indexOf(dayNameDate) - 1) == -1 ? 6 : days.indexOf(dayNameDate) - 1;
			// 	}
			// }

			//Generos Anime
			let genres = [];
			data?.data?.genres?.forEach(genre => {
			    let findGenre = genresDB.find(gene => gene[0][0] == genre.name);
				if(!findGenre){
					return;
				}
				genres.push(findGenre[0][1]);
			});
			data?.data?.themes?.forEach(genre2 => {
			    let findGenre2 = genresDB.find(gene2 => gene2[0][0] == genre2.name);
				if(!findGenre2){
					return;
				}
				genres.push(findGenre2[0][1]);
			});
			data?.data?.demographics?.forEach(genre3 => {
			    let findGenre3 = genresDB.find(gene3 => gene3[0][0] == genre3.name);
				if(!findGenre3){
					return;
				}
				genres.push(findGenre3[0][1]);
			});
			$('.genres').val(genres).trigger('change');

			//Clasificación Anime
			let rating = ratingDB.find(rt => rt[0][0] == data?.data?.rating);
			if(rating){
				document.getElementById("rating").value = rating[0][1];
			}else{
				document.getElementById("rating").value = 'No definido';
			}

			//Popularidad Anime
			let popularity = data?.data?.popularity;
			document.getElementById("popularity").value = popularity;

			//Promedio Votos Anime
			let vote_average = data?.data?.score;
			document.getElementById("vote_average").value = vote_average;

			//Youtube Trailer Anime
			let trailer = data?.data?.trailer?.embed_url;
			document.getElementById("trailer").value = trailer;
      	
      		$('#exampleModal').modal('hide');
    	})
    }
</script>
@endsection