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
use Illuminate\Support\Facades\Crypt;

class AppController extends Controller
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

	public function getRecentApp(Request $request)
	{
		try {
			return array(
			    'animes' => $this->anime->getNewAnimes($request),
			    'episodes' => $this->episode->getNewEpisodes($request),
			    'servers' => $this->server->getServersList2($request),
			    'players' => $this->player->getNewPlayers($request)
			);
		} catch (Exception $e) {
			return array(
	            'msg' => $e->getMessage()
	        );
		}
	}

    public function redirect(Request $request)
    {
		$enlaceCifrado = Crypt::encryptString('https://filemoon.sx/e/3bfr3p5e84mb/TDL12YLFOTIMRTKG4	');
        $token = "base64:/wYu7sqK2iXFksr7q8LsSgfk1NqaoIcb51uQBZq2YbU=";

        try {
            $url = Crypt::decryptString($token);
            return redirect()->away($url);
        } catch (\Exception $e) {
            abort(404);
        }
    }
}