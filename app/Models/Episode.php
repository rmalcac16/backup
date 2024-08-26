<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use Rennokki\QueryCache\Traits\QueryCacheable;

class Episode extends Model
{

    use QueryCacheable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = false; 
	public $updated_at = true;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'anime_id' => 'integer',
    ];

    public function players()
    {
        return $this->hasMany(\App\Models\Player::class);
    }

    public function anime()
    {
        return $this->belongsTo(\App\Models\Anime::class);
    }

    // Nuevas rutas app2

    public function app2_obtenerEpisodios() {
        return $this->cacheFor(now()->addHours(24))
            ->select('anime_id','number')
            ->limit(24)
            ->with(['anime' => function ($q) {
                $q->select('id','name','slug','banner');
            }])
            ->orderby('created_at','desc')
            ->get();
    }

    public function app2_obtenerEpisodiosLatinos() {
        return $this->cacheFor(now()->addHours(24))
            ->select('anime_id','number')
            ->leftJoin('players','players.episode_id','episodes.id')
            ->where('players.languaje',1)
            ->limit(24)
            ->distinct()
            ->with(['anime' => function ($q) {
                $q->select('id','name','slug','banner');
            }])
            ->orderby('players.created_at','desc')
            ->get();
    }

    public function app2_animeEpisodes($request)
    {
        return $this->cacheFor(now()->addDays(1))
            ->select('number','banner','episodes.created_at')
            ->where('animes.slug',$request->anime_slug)
            ->join('animes','animes.id','episodes.anime_id')
            ->orderBy('episodes.id','desc')
            ->get();
    }

	public function getInfoEpisodePage($request, $anime)
    {
        return $this
            ->select('id','number','views')
		    ->where('anime_id',$anime->id)
			->where('number',$request->episode_number)
			->first();
    }		
	
	public function getAnteriorEpisodePage($request, $anime)
    {
        return $this->select('number')
			->where('anime_id',$anime->id)
			->where('number',$request->episode_number-1)
			->first();
    }	

	public function getSiguienteEpisodePage($request, $anime)
    {
        return $this
            ->select('number')
			->where('anime_id',$anime->id)
			->where('number',$request->episode_number+1)
			->first();
    }

	public function getReleases()
    {
        return $this->cacheFor(now()->addHours(24))
            ->select('animes.name', 'animes.slug','animes.banner', 'animes.poster', 'players.created_at', 'episodes.number', 'players.languaje')
            ->leftJoin('players','players.episode_id','episodes.id')
            ->leftJoin('animes', 'animes.id', 'episodes.anime_id')
            ->where('episodes.id', '<=', 21844)
            ->where('animes.status', 1)
            ->groupBy('players.languaje', 'episodes.id')
		    ->orderBy('players.id', 'desc')
            ->limit(30)
			->get();
    }

    //Version 1.0.4
    public function getNewEpisodes($request)
    {
        return $this
            ->select('id', 'number', 'anime_id', 'created_at')
            ->where('id', '>=', 24298)
            ->where('id', '<=', 24298)
		    ->orderby('episodes.id','desc')
			->get();
    }
}
