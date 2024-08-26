<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Animelhd\AnimesFavorite\Traits\Favoriteable;
use Animelhd\AnimesView\Traits\Vieweable;
use Animelhd\AnimesWatching\Traits\Watchingable;

use Rennokki\QueryCache\Traits\QueryCacheable;

use DB;

class Anime extends Model
{
    use Sluggable;

	use QueryCacheable;

	use Favoriteable;
	use Vieweable;
	use Watchingable;

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
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'aired',
    ];

    public function episodes()
    {
        return $this->hasMany(\App\Models\Episode::class);
    }

    public function mylist()
    {
        return $this->hasMany(\App\Models\MyList::class);
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

	// Nuevas rutas app2

	public function app2_animesPopulars()
    {
        return $this->cacheFor(now()->addHours(1))
			->select('name','slug','banner','genres')
			->orderBy('views','desc')
			->limit(6)
			->get();
    }

	public function app2_animesFinalizados()
    {
        return $this->cacheFor(now()->addHours(1))
			->select('name','slug','poster')
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('players.languaje', 0)
			->where('status', 0)
			->groupBy('animes.id')
			->orderBy('animes.id','desc')
			->limit(14)
			->get();
    }

	public function app2_animesRecientes()
    {
        return $this->cacheFor(now()->addHours(1))
			->select('name','slug','poster')
			->where('status', 0)
			->orderBy('animes.id','desc')
			->limit(14)
			->get();
    }

	public function app2_animesLatinos()
    {
        return $this->cacheFor(now()->addHours(1))
			->select('name', 'slug', 'poster', 'vote_average','status',
		     \DB::raw('MAX(number) as number'),\DB::raw('MAX(players.id) as idplayer'))
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('players.languaje', 1)
			->groupBy('animes.id')
			->orderBy('idplayer','desc')
			->limit(14)
			->get();
    }

	public function app2_getAnimeInfoPage($request)
    {
        return $this->where('slug',$request->anime_slug)->first();
    }

	public function app2_getRecommendations($anime)
    {
		$first_name = explode(' ',trim($anime->name));
		$first_name = $first_name[0];

		$genres = explode(',',trim($anime->genres));
		$first_genre = '';
		$second_genre = '';

		if(count($genres) >= 2){
			$randoms = array_rand($genres, 2);
			$first_genre = $genres[$randoms[0]];
			$second_genre = $genres[$randoms[1]];
		}

        return $this->select('name','slug','banner')
			->where('genres','LIKE',"%{$first_genre}%")
			->where('genres','LIKE',"%{$second_genre}%")
			->where('slug','!=',$anime->slug)
			->limit(10)
			->inRandomOrder()
			->get();
    }

	public function app2_animesSearch($request)
    {
		return $this->select('name', 'slug', 'poster')
			->orderBy('name')
			->where('name','LIKE',"%{$request->search}%")
			->orwhere('name_alternative','LIKE',"%{$request->search}%")
			->orwhere('overview','LIKE',"%{$request->search}%")
			->limit(24)
			->get();
    }

	public function app2_getListAnimes($request)
    {
        $data = $this
			->select('name', 'slug', 'poster', 'aired')
			->orderBy('aired','desc');
		if($request->type){
			if($request->type != 'all')
				$data = $data->where('type',$request->type);
		}
		if(isset($request->status)){
			$data = $data->where('status',$request->status);
		}
		if($request->year){
			if(is_numeric($request->year)){
				$data = $data->whereYear('aired',$request->year);
			}
		}
		if($request->genre){
			if($request->genre != 'all')
				$data = $data->where('genres','LIKE',"%{$request->genre}%");
		}
		$data = $data->simplePaginate(28);
		return $data;
    }
	

	
	public function getBeingWatched()
    {
        return $this->cacheFor(now()->addHours(24))
			->select([
				\DB::raw("sum(episodes.views) as totalviews")
			,'name','slug','poster','aired'])
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->groupBy('animes.id')
			->orderBy('totalviews','desc')
			->limit(14)			
			->get();
    }	
	public function getPopularToday()
    {
        return $this->cacheFor(now()->addHours(24))
			->select('name','slug','poster','vote_average','aired')
            ->orderBy('vote_average','desc')			
			->limit(28)
			->get();
    }	

	public function getAnimesLatino()
    {
        return $this->cacheFor(now()->addHours(12))
			->select('name', 'slug', 'poster', 'vote_average','status',
		     \DB::raw('MAX(number) as number'),\DB::raw('MAX(players.id) as idplayer'))
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('episodes.id', '<=', 21844)
			->where('players.languaje', '=', 1)
			->groupBy('animes.id')
			->orderBy('idplayer','desc')
			->get();
    }


	public function getListAnimes($request)
    {
        $data = $this->select('name', 'slug', 'poster', 'aired', 'vote_average')
		->orderBy('aired','desc');
		if($request->type){
			$data = $data->where('type',$request->type);
		}
		if(isset($request->status)){
			$data = $data->where('status',$request->status);
		}
		if($request->year){
			$data = $data->whereYear('aired',$request->year);
		}
		if($request->genre){
			$data = $data->where('genres','LIKE',"%{$request->genre}%");
		}
		if($request->search){
			$data = $data->where('name','LIKE',"%{$request->search}%");
			$data = $data->orwhere('name_alternative','LIKE',"%{$request->search}%");
		}
		$data = $data->simplePaginate(28);
		return $data;
    }
	
	public function getUpcomingEpisodes()
    {
        return $this->cacheFor(now()->addHours(12))
			->where('status',1)
			->select('name','slug','banner','broadcast',
				\DB::raw('(select created_at from episodes where anime_id = animes.id order by number desc limit 1) as date'),
				\DB::raw('(select HOUR(created_at) from episodes where anime_id = animes.id order by number desc limit 1) as hour'),
				\DB::raw('(select number from episodes where anime_id = animes.id order by number desc limit 1) as lastEpisode'))
			->orderBy('hour','asc')
			->get()
			->groupBy('broadcast');
    }

	public function getSearch($request)
    {
        return $this->select('name','slug','type','poster')
			->where('name','LIKE',"%{$request->search}%")
			->orwhere('name_alternative','LIKE',"%{$request->search}%")
			->orwhere('overview','LIKE',"%{$request->search}%")
			->orwhere('genres','LIKE',"%{$request->search}%")
			->orwhere('aired','LIKE',"%{$request->search}%")
	        ->orderBy('aired','desc')
	        ->limit(20)
			->get();
    }	
	
	public function getAnimePage($request)
    {
        return $this
			->select(['animes.*',
				\DB::raw("IFNULL(sum(episodes.views),0) as totalviews")
			])
			->with(['episodes' => function ($q) {
				$q->orderBy('number', 'desc');
				$q->select('id','anime_id','number','views','created_at');
			}])
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->where('slug',$request->anime_slug)			
			->groupBy('animes.id')
			->first();
    }

    /**
     * Anime Page
     */	

	public function getAnimeInfoPage($request)
    {
        return $this
			->where('slug',$request->anime_slug)			
			->first();
    }

	public function getAnimeEpisodePage($request)
    {
        return $this->cacheFor(now()->addHours(24))
			->select('id','name','slug','banner','poster')
			->where('slug',$request->anime_slug)			
			->first();
    }

	public function getAnimeId($request)
    {
        return $this
			->select('animes.id', \DB::raw("sum(episodes.views_app) as totalviews"))
			->leftJoin('episodes','episodes.anime_id','=','animes.id')
			->where('animes.id',$request->id)	
			->groupBy('animes.id')		
			->first();
    }

	/**
     * Filtros
     */
	public function getRecommendations($anime)
    {
		$first_name = explode(' ',trim($anime->name));
		$first_name = $first_name[0];

		$genres = explode(',',trim($anime->genres));
		$first_genre = '';
		$second_genre = '';

		if(count($genres) >= 2){
			$randoms = array_rand($genres, 2);
			$first_genre = $genres[$randoms[0]];
			$second_genre = $genres[$randoms[1]];
		}

        return $this->select('name','slug','banner')
			->where('genres','LIKE',"%{$first_genre}%")
			->where('genres','LIKE',"%{$second_genre}%")
			->where('slug','!=',$anime->slug)
			->limit(10)
			->inRandomOrder()
			->get();
    }
	
    /**
     * Filtros
     */
	public function getFilterYears()
    {
        return $this->cacheFor(now()->addHours(24))
			->select(\DB::raw('YEAR(aired)as year'))
			->distinct()
			->orderBy('aired','desc')
			->get();
    }
	public function getFilterTypeAnime()
    {
        return $this->cacheFor(now()->addHours(24))
			->select(\DB::raw('type'))
			->distinct()
			->get();
    }	
	public function getFilterStatusAnime()
    {
        return $this->cacheFor(now()->addHours(24))
			->select(\DB::raw('status'))
			->distinct()
			->get();
    }
	
	//Version 1.0.5
	public function getNewAnimes($request)
    {
        return $this
			->select('id', 'status', 'vote_average', 'views_app as visitas', 'prequel', 'sequel', 'related', 'updated_at')
			->where('updated_at', '>=', $request->updatedAnimes)
			->where('id', '<=', 1401)
			->orderBy('aired','desc')
			->get();
	}
}
