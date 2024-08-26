<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Episode;
use App\Models\Player;
use App\Models\Server;
use App\Models\User;
use App\Models\Codigo;

use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Mail\SendCodeRestorePassword;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    /**
     * model instances.
     */
    protected $episode, $anime, $genre, $player, $server, $user;	

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Episode;  $episode
	 * @param  \App\Models\Anime;  $anime
	 * @param  \App\Models\Genre;  $genre
     * @return void
     */	
	public function __construct(Episode $episode, Anime $anime, Genre $genre, Player $player, Server $server, User $user)
	{
		$this->episode = $episode;
		$this->anime = $anime;
		$this->genre = $genre;
		$this->player = $player;
		$this->server = $server;
		$this->user = $user;
	}
	
	public $url;

	public function getDataHome(Request $request)
	{
		try {
			$data = array(
				'episodes_recents' => $this->episode->app2_obtenerEpisodios(),
				'animes_populars' => $this->anime->app2_animesPopulars(),
				'animes_latino' => $this->anime->app2_animesLatinos(),
				'animes_recents' => $this->anime->app2_animesRecientes(),
				'animes_finished' => $this->anime->app2_animesFinalizados()
			);
			return array(
				'status' => true,
				'data' => $data
			);
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function getDataAnime(Request $request)
	{
		try {
			$anime = $this->anime->app2_getAnimeInfoPage($request);
			return array(
				'status' => true,
				'data' => $anime,
				'recomendations' => $this->anime->app2_getRecommendations($request)
			);
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function getAnimeRecommendations(Request $request)
	{
		try {
			$anime = $this->anime->getAnimeInfoPage($request);
			return $this->anime->getRecommendations($anime);
		} catch (Exception $e) {
			return array(
	            'code' => $e->getMessage()
	        );
		}
	}

	public function getEpisodesAnime(Request $request)
	{
		try {
			return array(
				'status' => true,
				'data' => $this->episode->app2_animeEpisodes($request)
			);;
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function getAnimesSearch(Request $request)
	{
		try {
			return array(
				'status' => true,
				'data' => $this->anime->app2_animesSearch($request)
			);;
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function getAnimesList(Request $request)
	{
		try {
			return array(
				'status' => true,
				'data' => $this->anime->app2_getListAnimes($request)
			);;
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function getPlayersEpisode(Request $request)
	{
		try {
			return array(
				'status' => true,
				'data' => $this->player->app2_getPlayersEpisode($request)
			);;
		} catch (Exception $e) {
			return array(
				'status' => false,
	            'msg' => 'Hubo un error inesperado',
				'error' => $e->getMessage()
	        );
		}
	}

	public function releases(Request $request)
	{
		try {
			return $this->episode->getReleases();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function trending(Request $request)
	{
		try {
			return array(
			    'popular_today' => $this->anime->getPopularToday()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function moreView(Request $request)
	{
		try {
			return array(
			    'being_watched' => $this->anime->getBeingWatched(),
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}	
	
	public function filterings(Request $request)
	{
	    try {
			return array(
			    'years' => $this->anime->getFilterYears(),
			    'genres' => $this->genre->getFilterGenres(),
			    'types' => $this->anime->getFilterTypeAnime(),
			    'status' => $this->anime->getFilterStatusAnime()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function latino(Request $request){
	    try {
	        return $this->anime->getAnimesLatino();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function listAnimes(Request $request)
	{
		try {
		    return $this->anime->getListAnimes($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function simulcast(Request $request)
	{
		try {
			return $this->anime->getUpcomingEpisodes();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function search(Request $request)
	{
		try {
			return $this->anime->getSearch($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getAnime(Request $request)
	{
		try {
			$data = $this->anime->getAnimePage($request);
	       	if(!$data){
				return redirect('404');
	        }
			return $data;
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getAnimeEpisodes(Request $request)
	{
		try {
			$data = $this->anime->getAnimePage($request);
	       	if(!$data){
				return redirect('404');
	        }
			return $data;
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	
	public function getEpisode(Request $request)
	{
	    try{
	    	$animed = $this->anime->getAnimeEpisodePage($request);
	    	if(!$animed){
	            return redirect('404');
	        }
	        $episoded = $this->episode->getInfoEpisodePage($request, $animed);
	        if(!$episoded){
	            return redirect('404');
	        }
			DB::unprepared('update episodes set views = views+1 where id = '.$episoded->id.'');
	        $anterior = $this->episode->getAnteriorEpisodePage($request, $animed);
	        $siguiente = $this->episode->getSiguienteEpisodePage($request, $animed);
	        $episoded->anime = $animed;
	        $episoded->anterior = $anterior;
	        $episoded->siguiente = $siguiente;
	        $players = $this->player->getPlayersEpisode($request, $episoded);
	        $episoded->players = $players;
	        return $episoded;
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function getEpisodePlayers(Request $request)
	{
	    try{
	    	$animed = $this->anime->getAnimeEpisodePage($request);
	    	if(!$animed){
	            return redirect('404');
	        }
	        $episoded = $this->episode->getInfoEpisodePage($request, $animed);
	        if(!$episoded){
	            return redirect('404');
	        }
			DB::unprepared('update episodes set views = views+1 where id = '.$episoded->id.'');
	        $anterior = $this->episode->getAnteriorEpisodePage($request, $animed);
	        $siguiente = $this->episode->getSiguienteEpisodePage($request, $animed);
	        $episoded->anime = $animed;
	        $episoded->anterior = $anterior;
	        $episoded->siguiente = $siguiente;
	        $players = $this->player->getPlayersEpisodeNew($request, $episoded);
	        $episoded->players = $players;
	        return $episoded;
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function setViewsAnime(Request $request)
	{
	    try{
	    	$animedi = $this->anime->getAnimeId($request);
			//DB::unprepared('update animes set views = '.$animedi->totalviews.' where id = '.$request->id.'');
			//DB::unprepared('update episodes set views_app = views where id = '.$request->episode_id.'');
			DB::unprepared('update episodes set views_app = views_app+1 where id = '.$request->episode_id.'');
			return array(
				'status' => true,
			);
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function setViewsAnimes(Request $request)
	{
	    try{
	    	$animedi = $this->anime->getAnimeId($request);
			DB::unprepared('update animes set views_app = '.$animedi->totalviews.' where id = '.$request->id.'');
			//DB::unprepared('update episodes set views_app = views where id = '.$request->episode_id.'');
			return array(
				'status' => true,
			);
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	
	public function sitemap(Request $request){
		try {
			$animes = Anime::orderBy('aired','desc')
			->select('slug', 'status')
			->get();
			$episodios = Episode::orderBy('episodes.created_at', 'desc')
			->select('slug', 'number', 'status')
			->leftJoin('animes','animes.id','=','anime_id')
			->get();			
			return array(
			    'animes' => $animes,
			    'capitulos' => $episodios,
			);	
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}   	

	/**
     * Api Login
     *
     */

	public function getTokenLogin(Request $request){
		try {
			return $this->user->getToken($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}  

	public function loginUser(Request $request){
		try {
			return $this->user->login($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}

	public function getRegister(Request $request) {
		try {
			return $this->user->register($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}			
	}

	public function logoutUser(Request $request){
		try {
			return array(
				'code' => 200,
	            'status' => $this->user->logout($request)
	        );
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}

	public function forgotPassword(Request $request){
		try {
			return $this->user->forgotPassword($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	}

	public function addFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->favorite($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404,
				'error' => $e->getMessage()
	        );
		}		
	}

	public function deleteFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unfavorite($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listFavoriteAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getFavoriteItems(Anime::class)->cacheFor(now()->addHours(1))->select('id','name','slug','poster', 'isTopic')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}
	
	public function addViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->view($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function deleteViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unview($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listViewAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getViewItems(Anime::class)->select('id','name','slug','poster', 'isTopic')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}

	public function addWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->watching($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function deleteWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unwatching($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}

	public function listWatchingAnime(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getWatchingItems(Anime::class)->select('id','name','slug','poster', 'isTopic')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}

	//EndPoints App
	public function getRecenteApp()
	{
		try {
			return array(
			    'animes' => $this->anime->getAnimesRecent(),
			    'episodes' => $this->episode->getEpisodesRecent(),
			    'servers' => $this->server->getServersList(),
			    'players' => $this->player->getPlayersRecent()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getNewApp()
	{
		try {
			return redirect('https://www.google.com.co/', 301);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getListApp2()
	{
		try {
			return redirect('https://www.google.com.co/', 301);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//App Nueva
	public function getServerApp(Request $request)
	{
		try {
			return $this->server->getServerApp($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getPlayerApp(Request $request)
	{
		try {
			return $this->player->getPlayerApp($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}	
	//Fin App Nueva	
	//Lista de Animes EndPoint App Nueva
	public function getAnimeList()
	{
		try {
			return $this->anime->getAnimeList();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//Lista de Episodes EndPoint App Nueva
	public function getEpisodeList()
	{
		try {
			return $this->episode->getEpisodeList();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//Lista de Servers EndPoint App Nueva
	public function getServersList(Request $request)
	{
	    try{
 	        return $this->server->getServersList();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	//Lista de Players EndPoint App Nueva
	public function getPlayerList()
	{
	    try{
 	        return $this->player->getPlayerList();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	public function getLastPlayer2()
	{
	    try{
			return $this->player->getLastPlayer2();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}

	public function getTokenApp(Request $request){
		try {
			return $this->user->getTokenApp($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}		
	} 
	
	public function addReportPlayer(Request $request){
		try {
			$data = $this->player::select('animes.name as anime','episodes.number','servers.title as server','players.id as player_id')
				->where('players.id', $request->player_id)
				->join('episodes','episodes.id','players.episode_id')
				->join('animes','animes.id','episodes.anime_id')
				->join('servers','servers.id','players.server_id')
				->first();
			$server = strtolower($data->server);
			if($server != "dseta"){
				if(!DB::table('reportes')->where('player_id', $request->player_id)->exists()){
					DB::table('reportes')->insert($data->toArray());
				}
				return array(
					'code' => 200,
				    'status' => true,
					'data' => $data
				);
			}else{
				return array(
					'code' => 200,
				    'status' => false,
					'data' => $data
				);
			}
		} catch (Exception $e) {
			return array(
				'code' => 400,
	            'status' => false
	        );
		}
	}

	public function config(Request $request)
	{
		if($request->get('v') == '3.2.4'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '3.2.3'){
			return array(
				'status' => true,
			);
		}
		else if($request->get('v') == '3.2.2'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '3.2.1'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '3.2.0'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '3.1.4'){
			return array(
				'status' => false,
			);
		}else if($request->get('v') == '3.1.1'){
			return array(
				'videos' => false,
				'imagenes' => false,
				'updates' => true,
				'latinos' => true,
				'perfil' => false,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else if($request->get('v') == 302){
			return array(
				'videos' => false,
				'imagenes' => true,
				'updates' => true,
				'latinos' => true,
				'perfil' => true,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else if($request->get('v') == 303){
			return array(
				'videos' => false,
				'imagenes' => false,
				'updates' => false,
				'latinos' => false,
				'perfil' => false,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}else {
			return array(
				'videos' => true,
				'imagenes' => true,
				'updates' => true,
				'latinos' => true,
				'perfil' => true,
				'mensaje' => 'Hay una actualizacion disponible - Reinicia esta aplicación.',
			);
		}
	}

	public function version(Request $request)
	{
		if($request->get('v') == '1.0.3'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '1.0.2'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '1.0.1'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '1.0.0'){
			return array(
				'status' => true,
			);
		}

	}
	//Nueva App Tienda y V1.0.3
	public function getRecentApp()
	{
		try {
			return array(
			    'animes' => $this->anime->getRecentAnime(),
			    'episodes' => $this->episode->getRecentEpisodes(),
			    'servers' => $this->server->getServersList2(),
			    'players' => $this->player->getRecentPlayers()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getListApp()
	{
		try {
			return array(
			    'animes' => $this->anime->getAnimesList2(),
			    'episodes' => $this->episode->getEpisodesList2(),
				'servers' => $this->server->getServersList2(),
			    'players' => $this->player->getPlayersList2()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getLastPlayer()
	{
	    try{
 	        return $this->player->getLastPlayer();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	public function addAnimeFavorite(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->favorite($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404,
				'error' => $e->getMessage()
	        );
		}		
	}
	public function deleteAnimeFavorite(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unfavorite($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}
	public function listAnimeFavorite(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getFavoriteItems(Anime::class)->cacheFor(now()->addHours(1))->select('id','name','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}
	public function addAnimeView(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->view($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}
	public function deleteAnimeView(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unview($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}
	public function listAnimeView(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getViewItems(Anime::class)->cacheFor(now()->addHours(1))->select('id','name','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}
	public function addAnimeWatching(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->watching($anime);
			return array(
				'code' => 200,
	            'status' => true
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}
	public function deleteAnimeWatching(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$anime = $this->anime::find($request->anime_id);
			$user->unwatching($anime);
			return array(
				'code' => 200,
	            'status' => false
	        );
		} catch (Exception $e) {
			return array(
	            'code' => 404
	        );
		}		
	}
	public function listAnimeWatching(Request $request){
		try {
			$user = $this->user::find($request->user_id);
			$data = $user->getWatchingItems(Anime::class)->cacheFor(now()->addHours(1))->select('id','name','poster')->orderBy('name','asc')->get();
			return $data;
		} catch (Exception $e) {
			return array(
	            'status' => false
	        );
		}		
	}



	public function codePasswordRestore(Request $request){
		try {
			$validated = $request->validate([
				'email' => 'required|email|exists:users,email'
			], [
				'email.exists' => 'El correo electrónico no se encuentra en nuestros registros'
			]);
			$user = User::where('email',$request->email)->first();

			$codigoExistente = Codigo::where('user_id',$user->id)->first();

			if ($codigoExistente && now() < Carbon::Parse($codigoExistente->created_at)->addMinutes(5)) {
				throw new Exception("Ya se envió un código al correo electrónico o espera un momento para generar otro.", 401);
			}else {
				$codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
				$response = Mail::to($user->email)->send(new SendCodeRestorePassword($codigo));
				if($codigoExistente){
					$codigoExistente->delete();
				}
				$sendCode = new Codigo([
					'codigo' => $codigo,
					'user_id' => $user->id,
					'expires_at' => now()->addMinutes(30)
				]);
				$sendCode->save();
				$data = array(
					'status'=> true,
					'message' => 'El codigo ha sido enviado con éxito',
					'user_id' => $user->id,
					'user_email' => $user->email,
				);
				return response()->json($data, 200);
			}
		} catch (ValidationException $exception) {
			return response()->json([
				'status' => false,
				'message'	=> 'Error',
				'errors' => $exception->errors(),
			], 422);
		} catch (Exception $e) {
			return response()->json(array(
	            'status' => false,
				'message' => $e->getMessage()
	        ));
		}		
	}


	public function passwordRestore(Request $request){
		try {

			$request->validate([
				'user_id' => 'required|exists:users,id',
				'user_email' => 'required|email|exists:users,email',
				'code' => 'required|numeric|digits:6',
				'password' => 'required|confirmed|min:8',
			], [
				'user_id.exists' => 'El usuario no se encuentra en nuestros registros',
				'user_email.exists' => 'El correo electrónico no se encuentra en nuestros registros',
			]);

			$userCode = Codigo::where('user_id',$request->user_id)->where('codigo',$request->code)->first();

			if(!$userCode)
				throw new Exception("El codigo ingresado no es correcto", 403);

			if($userCode->expires_at < now())	
				throw new Exception("El codigo ha expirado", 403);
			
			$user = User::where('email', $request->user_email)->update(['password' => Hash::make($request->password)]);

			$userCode->delete();

			$data = array(
				'status'=> true,
				'message' => 'Se ha actualizado tu contraseña con exito'
			);

			return response()->json($data, 200);

		}catch (ValidationException $exception) {
			return response()->json([
				'status' => false,
				'message'    => 'Error',
				'errors' => $exception->errors(),
			], 422);
		} catch (Exception $e) {
			return array(
	            'status' => false,
				'message' => $e->getMessage()
	        );
		}		
	}

}