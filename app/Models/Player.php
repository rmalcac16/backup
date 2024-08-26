<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Rennokki\QueryCache\Traits\QueryCacheable;

class Player extends Model
{

	use QueryCacheable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = true;
	public $updated_at = true;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'server_id' => 'integer',
        'episode_id' => 'integer',
    ];


    public function server()
    {
        return $this->belongsTo(\App\Models\Server::class);
    }

    public function episode()
    {
        return $this->belongsTo(\App\Models\Episode::class);
    }


	public function app2_getPlayersEpisode($request)
    {

		$results = $this
			->select('code', 'server_id', 'languaje', 'title', 'embed', 'servers.status')
			->where('animes.slug', $request->anime_slug)
			->where('episodes.number', $request->episode_number)
			->join('episodes', 'episodes.id', 'players.episode_id')
			->join('animes', 'animes.id', 'episodes.anime_id')
			->join('servers', 'servers.id', 'players.server_id')
			->get();

		$groupedData = $results->groupBy('languaje')->map(function ($group) {
			return $group->values();
		});

		$groupedData = $groupedData->all(); // Convertir el resultado en un array simple

		$response = [];

		// Establecer los índices 0 y 1
		$response[0] = $groupedData[0] ?? [];
		$response[1] = $groupedData[1] ?? [];

		return $response;
    }

	public function web_getEpisodesReleases()
    {
		return $this->cacheFor(now()->addHours(24))
			->select('animes.name', 'animes.slug','animes.banner', 'animes.poster', 'players.created_at', 'episodes.number', 'players.id', 'players.languaje')
			->leftJoin('episodes', 'episodes.id', 'players.episode_id')
			->leftJoin('animes', 'animes.id', 'episodes.anime_id')
			->join(\DB::raw("(SELECT animes.id as anime_id, MAX(episodes.id) as episode_id
						FROM episodes
						JOIN animes ON animes.id = episodes.anime_id
						GROUP BY animes.id) latest_episodes"), function ($join) {
							$join->on('latest_episodes.anime_id', '=', 'animes.id')
							->on('latest_episodes.episode_id', '=', 'episodes.id');
						})
			->leftJoin(\DB::raw("(SELECT episode_id, MAX(id) as max_player_id
						FROM players
						GROUP BY episode_id) max_players"), function ($join) {
							$join->on('max_players.episode_id', '=', 'episodes.id');
						})
			->whereColumn('max_players.max_player_id', '=', 'players.id')
			->where('episodes.id', '<=', 21844)
			->where('animes.status', 1)
			->limit(24)
			->distinct('episodes.id')
			->orderBy('players.id', 'desc')
			->get();
	}
	
	public function getPlayersEpisode($request, $episode)
    {
        return $this
			->select('players.id','code','languaje','server_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id',$episode->id)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 3);
			})
			->with(['server'])
			->get()
			->groupby('languaje');
    }

    public function getPlayersEpisodeNew($request, $episode)
    {
        return $this
			->select('players.id','languaje','server_id')
			->leftJoin('servers','servers.id','=','players.server_id')
			->where('episode_id',$episode->id)
			->where(function ($query) {
				$query->where('status', 1)
					  ->orWhere('status', 2);
			})
			->with(['server'])
			->get()
			->groupby('languaje');
    }
    
	//Version 1.0.5
	public function getNewPlayers($request)
    {
        return $this
			->select('players.id', 'code as link', 'languaje as language', 'server_id', 'episode_id', 'updated_at')
			->leftJoin('servers','servers.id','=','players.server_id')
            ->where('updated_at', '>=', $request->updatedPlayers)
			->where('players.id', '<=', 66887)
			->orderby('episode_id','desc')
			->orderby('players.id','desc')
			->get();
    }
}