@extends('admin.layout')

@section('styles')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_custom.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

<style>
.lds-ring {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 64px;
  height: 64px;
  margin: 8px;
  border: 8px solid #fff;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #fff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
@endsection

@section('content')
<div class="row layout-spacing">
	<div class="col-lg-12">
		@include('admin.inc.flash-message')
		<div class="statbox widget box box-shadow">
			<div class="widget-content widget-content-area">
				<div class="row">
					<div class="col-12 col-md-10">
						<a class="btn btn-sm btn-success mb-2" href="{{ route('admin.animes.episodes.create',[$anime->id]) }}">{{ __('Add') }}</a>
						<a class="btn btn-sm btn-info mb-2" href="{{ route('admin.animes.episodes.generate',[$anime->id]) }}">{{ __('Generate') }}</a>
						<a class="btn btn-sm btn-danger mb-2" onclick="event.preventDefault();document.getElementById('delete_all_episodes').submit();">{{ __('All delete') }}</a>
						<form id="delete_all_episodes" action="{{ route('admin.animes.episodes.allDelete',[$anime->id]) }}" method="POST" style="display: none;">
							@csrf
						</form>
						<a class="btn btn-sm btn-primary mb-2" href="{{ route('admin.animes.episodes.generatePlayers',[$anime->id]) }}">{{ __('Generate players') }}</a>
						<a class="btn btn-sm btn-danger mb-2" data-toggle="modal" data-target="#deletePlayers">{{ __('Delete players') }}</a>
					</div>
					<div class="col-12 col-md-2 text-right">
						<a class="btn btn-sm btn-success mx-1" data-toggle="modal" data-target="#importador">{{ __('Import') }}</a>
					</div>
				</div>
				<div class="table-responsive mb-4">
					<table id="style-3" class="table style-3 table-hover">
						<thead>
							<tr>
								<th>{{ __('Title') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Views') }}</th>
								<th>{{ __('Views') }}</th>
                                <th class="text-center">{{ __('Options') }}</th>
							</tr>
						</thead>
						<tbody>
							@forelse($episodes as $episode)
                                <tr>
                                	<td>
                                		<a href="{{ route('admin.animes.episodes.players.index',[$anime->id, $episode->id]) }}">{{ $anime->name.' '.$episode->number }}</a>
                                	</td>
                                	@php $date = new DateTime($episode->created_at) @endphp
                                    <td>{{ $date->format('d M Y') }}</td>
									<td>{{ $episode->views }}</td>
                                    <td>{{ $episode->views_app }}</td>
                                    <td class="text-center">
									<ul class="table-controls">
										<li>
											<a href="{{ route('admin.animes.episodes.edit',[$anime->id, $episode->id]) }}" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{__('Edit')}}">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-6 mb-1">
													<path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
												</svg>
											</a>
										</li>
										<li>
											<a class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{__('Delete')}}" onclick="event.preventDefault(); document.getElementById('delete_episode_{{$episode->id}}').submit();"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
				                            <form id="delete_episode_{{$episode->id}}" action="{{ route('admin.animes.episodes.destroy',[$anime->id, $episode->id]) }}" method="POST" style="display: none;">
				                            	@method('DELETE')
				                                @csrf
                            				</form>
										</li>
									</ul>
								</td>
                                </tr>
                            @empty
                            @endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
    
<!-- Modal -->
<div class="modal fade" id="deletePlayers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body row">
                <form class="col" action="{{route('admin.animes.episodes.players.allDeletePlayers',[$anime->id])}}" method="POST">
                    @csrf
                    <input type="hidden" name="languaje" value="0" />
                    <button type="submit" id="deleteSub" class="btn btn-info mb-2 ml-3">{{ __('Delete subbed') }}</button>
                </form>
                <form class="col" action="{{route('admin.animes.episodes.players.allDeletePlayers',[$anime->id])}}" method="POST">
                    @csrf
                    <input type="hidden" name="languaje" value="1" />
                    <button type="submit" id="deleteLat" class="btn btn-info mb-2 ml-3">{{ __('Delete Latino') }}</button>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importador -->
<div class="modal fade" id="importador" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header">
				<div class="w-100 d-flex align-items-center justify-content-between">
					<div class="d-flex">
						@if($anime->slug_flv)
						<div class="d-flex flex-row align-items-center px-2 py-1 bg-primary">
							<input checked class="mr-2" type="radio" name="server_page" value="flv"/>Flv
						</div>
						@endif
						@if($anime->slug_tio)
						<div class="d-flex flex-row align-items-center px-2 py-1 bg-secondary">
							<input class="mr-2" type="radio" name="server_page" value="tio"/>Tio
						</div>
						@endif
						@if($anime->slug_jk)
						<div class="d-flex flex-row align-items-center px-2 py-1 bg-info">
							<input class="mr-2" type="radio" name="server_page" value="jk"/>Jk
						</div>
						@endif
						@if($anime->slug_monos)
						<div class="d-flex flex-row align-items-center px-2 py-1 bg-dark">
							<input class="mr-2" type="radio" name="server_page" value="monos"/>Monos
						</div>
						@endif
						@if($anime->slug_fenix)
						<div class="d-flex flex-row align-items-center px-2 py-1 bg-success">
							<input class="mr-2" type="radio" name="server_page" value="fenix"/>Fenix
						</div>
						@endif
					</div>
					<div>
						<button onclick="cargarImportador(`{{$anime->id}}`)" class="btn btn-sm btn-success">Cargar</button>
					</div>
				</div>
			</div>
            <div class="modal-body">
				<div id="results" style="min-height: 200px;" class="align-items-center justify-content-center resultado_anime"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm" data-dismiss="modal">{{ __('Cerrar') }}</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('aditionals')
	<script src="{{ asset('plugins/table/datatable/datatables.js') }}"></script>
	<script>
        $('#style-3').DataTable({
        	"oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando página _PAGE_ de _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
            },
            "ordering": false
        });
    </script>
	<script>
		function cargarImportador(idAnime) {
			const pageSelected = $('input[name=server_page]:checked').val();
			let url = '';
			let slugAnime = ''
			if(pageSelected.toLowerCase() == 'flv'){
				url = `{{route('import.flv.anime.slug')}}`;
				slugAnime = `{{$anime->slug_flv}}`;
			}else if(pageSelected.toLowerCase()== 'tio' ){
				url = `{{route('import.tio.anime.slug')}}`
				slugAnime = `{{$anime->slug_tio}}`;
			}else if(pageSelected.toLowerCase()== 'jk' ){
				url = `{{route('import.jk.anime.slug')}}`
				slugAnime = `{{$anime->slug_jk}}`;
			}else if(pageSelected.toLowerCase()== 'monos' ){
				url = `{{route('import.monos.anime.slug')}}`
				slugAnime = `{{$anime->slug_monos}}`;
			}else{
				url = `{{route('import.fenix.anime.slug')}}`
				slugAnime = `{{$anime->slug_fenix}}`;
			}
			url += `?id=${idAnime}&slug=${slugAnime}`;
			$("#results").html('<div class="d-flex justify-content-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>');
			fetch(url)
			.then(res => res.json())
			.then(data => {
				var items = '';
				var i = 1;
				$.each(data.pages, function(key,value){
					items += '<div class="mb-2"><div class="d-flex justify-content-between">';
					items += '<div>Del '+value.init+' al '+value.fin+'</div>'
					items += '<div><button class="btn btn-sm" onclick=(importVideosPorPage(this,"'+pageSelected.toLowerCase()+'",'+idAnime+',"'+slugAnime+'",'+value.init+','+value.fin+'))>{{ __("Page")}} '+i+'</button></div>'
					items += '</div></div>';
					i++;
				});
				$("#results").html(items);
			})
		}
	</script>
	<script>
		function importVideosPorPage(btn, server_page, idAnime, slugAnime, inicio, fin) {
			btn.innerText = "Cargando";
			btn.disabled = true;
			let url = '';
			if(server_page == 'flv'){
				url = `{{route('import.tio.anime.perpages')}}`
			}else if(server_page == 'tio' ){
				url = `{{route('import.tio.anime.perpages')}}`
			}else if(server_page == 'jk' ){
				url = `{{route('import.jk.anime.perpages')}}`
			}else if(server_page == 'monos' ){
				url = `{{route('import.monos.anime.perpages')}}`
			}else{
				url = `{{route('import.fenix.anime.perpages')}}`
			}
			url += `?id=${idAnime}&slug=${slugAnime}&inicio=${inicio}&fin=${fin}`;
			fetch(url)
			.then(res => res.json())
			.then(data => {
				if(data.status == 200){
					btn.innerText = "Ver Detalles";
					btn.disabled = false;
					btn.onclick = function(){
						var items = '';
						var i = 1;
						$.each(data.data, function(key,value){
							items += '<div class="d-flex justify-content-between p-2">';
							items += '<span>'+value?.anime+' - '+value?.episode+'</span>'
							items += '<span>Cantidad de reproductores: '+value?.players?.data?.length+'</span>'
							items += '</div>';
							i++;
						});
						const div = document.createElement('div');
						div.className = 'detalles';
						div.innerHTML = items;
						btn.parentElement.parentElement.parentElement.appendChild(div);
						btn.innerText = "Recargar";
						btn.onclick = function(){
							const elements = document.getElementsByClassName("detalles");
							if(elements.length > 0 )
								btn.parentElement.parentElement.parentElement.removeChild(elements[0]);
							importVideosPorPage(btn,pagina);
						};
					};
				}
				else{
					btn.innerText = "Error";
					btn.disabled = false;
				}
			})
		}
		function importAllCaps(id, slug_flv) {
			let loader = `<div class="d-flex justify-content-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>`;
			$("#results").html(loader);
			let url = "{{route('import.monos.anime.episodes')}}?id="+id+"&slug="+slug_flv;
			fetch(url)
			.then(response => response.json())
			.then(data => {
				$("#results").html(`<div class="text-center justify-content-center"><h2>${data.status != 200 ? 'Error!!' : 'Exito!!'}</h2><h6>Se importaron - ${Object.keys(data.data).length} episodios</h6></div>`);
			})
			.catch(error => {
				$("#results").html(`<h2>Hubo un error ${JSON.stringify(error)}</h2>`);
			})
		}
    </script>
@endsection