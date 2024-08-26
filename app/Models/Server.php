<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
	public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];
	
    //EndPoint App
    public function getServersList()
    {
        return $this->select('id', 'title as name', 'position', 'embed')	
			->get();
    }
    
    public function getServersList2()
    {
        return $this->select('id', 'title as name', 'position', 'embed', 'status')	
            ->where('id', 1)
			->get();
    }  
    //Version 1.0.4
    	public function getServerApp($request) 
	{
        return $this->select('players.id as id', 'title as name', 'position', 'number', 'anime_id')
			->leftJoin('players','servers.id','=','players.server_id')
			->leftJoin('episodes', 'episodes.id', '=', 'players.episode_id')
			->where('episodes.anime_id', $request->anime_id)
			->where('episodes.number', $request->episode_number)
			->where('players.languaje', $request->languaje)
			->where(function ($query) {
				$query->where('server_id', 1)
					  ->orWhere('server_id', 5);
			})			
			->orderby('servers.position','asc')
			->get();		
	}
}
