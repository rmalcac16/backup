<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];
	public $timestamps = false;

    /**
     * Filtros
     */
	public function getFilterGenres()
    {
        return $this->select('title','slug')
			->orderBy('title','asc')
			->get();	
    }
}
