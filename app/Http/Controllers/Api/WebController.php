<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Episode;
use App\Models\Player;
use App\Models\Server;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
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
			return array(
				'episodes' => $this->episode->getReleases(),
				'animeN' => $this->anime->getListAnimes($request),
				'animeP' => $this->anime->getPopularToday(),
				'animeV' => $this->anime->getBeingWatched()
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

	public function getDataAnime(Request $request)
	{
		try {
			return $this->anime->getAnimeInfoPage($request);
		} catch (Exception $e) {
			return array(
	            'code' => $e->getMessage()
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

	public function home(Request $request)
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
			if($request->header('Authorization') && $request->user_id){
				$user = $this->user::find($request->user_id);
				$data = $user->attachFavoriteStatus($data);
				$data = $user->attachSubscriptionStatus($data);
				unset($data->episodes);
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
			if($request->header('Authorization') && $request->user_id){
				$user = $this->user::find($request->user_id);
				foreach($data->episodes as $episode){
					$episode->viewed = $user->hasLiked($episode);
				}
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
			$data = $user->getFavoriteItems(Anime::class)->select('id','name','slug','poster', 'isTopic')->orderBy('name','asc')->get();
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
	public function getRecents()
	{
		try {
			return array(
			    'animes' => $this->anime->getAnimesRecentList(),
			    'episodes' => $this->episode->getEpisodesRecents(),
			    'servers' => $this->server->getServersList(),
			    'players' => $this->player->getPlayersRecents()
			);
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
	public function getAnimesRecentList()
	{
		try {
			return $this->anime->getAnimesRecentList();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getAnimesList(Request $request)
	{
		try {
			return $this->anime->getAnimesList($request);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	//Lista de Episodes EndPoint App Nueva
	public function getEpisodesRecentList()
	{
		try {
			return $this->episode->getEpisodesRecentList();
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}
	public function getEpisodesList(Request $request)
	{
		try {
			return $this->episode->getEpisodesList($request);
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
	public function getPlayersRecentList()
	{
	    try{
 	        return $this->player->getPlayersRecentList();
	    }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
	    }
	}
	public function getPlayersList(Request $request)
	{
	    try{
 	        return $this->player->getPlayersList($request);
	    }catch(Exception $e){
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
			if($server != "alpham"){
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
		if($request->get('v') == '1.0.1'){
			return array(
				'status' => true,
			);
		}else if($request->get('v') == '1.0.0'){
			return array(
				'status' => true,
			);
		}

	}

}