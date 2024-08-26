<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

use App\Models\Anime;
use App\Models\Genre;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $animes = Anime::orderby('id','desc')->get();
        $data = [
            'category_name' => 'animes',
            'page_name' => 'list',
            'animes' => $animes
        ];
        return view('admin.animes.list')->with($data);
    }

    public function indexLatino()
    {
        $animes = Anime::select('animes.*')
            ->leftjoin('episodes','episodes.anime_id','animes.id')
            ->leftjoin('players','players.episode_id','episodes.id')
            ->groupby('name')
            ->where('languaje',1)
            ->distinct()
            ->orderby('animes.id','desc')
            ->get();
        $data = [
            'category_name' => 'animes',
            'page_name' => 'listLatino',
            'animes' => $animes
        ];
        return view('admin.animes.listLatino')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $genres = Genre::all();
        $data = [
            'category_name' => 'animes',
            'page_name' => 'create',
            'genres' => $genres
        ];
        return view('admin.animes.create')->with($data);
    }

    public function generate()
    {
        $data = [
            'category_name' => 'animes',
            'page_name' => 'generate'
        ];
        return view('admin.animes.generate')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (Anime::where('slug',Str::slug($request->name))->exists()) {
                Anime::updateOrCreate([
                    'name' => $request->name,
                    'name_alternative' => $request->name_alternative,
                    'banner' => $request->banner,
                    'poster' => $request->poster,
                    'overview' => $request->overview,
                    'aired' => $request->aired,
                    'type' => $request->type,
                    'status' => $request->status,
                    'premiered' => $request->premiered,
                    'broadcast' => $request->broadcast,
                    'genres' => $request->genres ? implode(",", $request->genres) : '',
                    'rating' => $request->rating,
                    'popularity' => $request->popularity,
                    'vote_average' => $request->vote_average,
                    'trailer' => $request->trailer
                ]);
                return redirect()->route('admin.animes.index')->with('warning', 'Anime duplicado correctamente');
            }
            Anime::updateOrCreate([
                'name' => $request->name,
                'name_alternative' => $request->name_alternative,
                'banner' => $request->banner,
                'poster' => $request->poster,
                'overview' => $request->overview,
                'aired' => $request->aired,
                'type' => $request->type,
                'status' => $request->status,
                'premiered' => $request->premiered,
                'broadcast' => $request->broadcast,
                'genres' => $request->genres ? implode(",", $request->genres) : '',
                'rating' => $request->rating,
                'popularity' => $request->popularity,
                'vote_average' => $request->vote_average,
                'trailer' => $request->trailer,
                'slug_flv' => $request->slug_flv,
                'slug_tio' => $request->slug_tio,
                'slug_jk' => $request->slug_jk,
                'slug_monos' => $request->slug_monos,
                'slug_fenix' => $request->slug_fenix
            ]);
            return redirect()->route('admin.animes.index')->with('success', 'Anime añadido correctamente ');
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->with('error', $e->getMessage());
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $anime = Anime::where('id',$id)->first();
        if(!$anime){
            abort(404,"Not Found Anime");
        }
        $genres = Genre::all();
        $data = [
            'category_name' => 'animes',
            'page_name' => 'edit',
            'anime' => $anime,
            'genres' => $genres
        ];
        return view('admin.animes.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            Anime::updateOrCreate(['id' => $id], [
                'name' => $request->name,
                'name_alternative' => $request->name_alternative,
                'slug' => $request->slug,
                'banner' => $request->banner,
                'poster' => $request->poster,
                'overview' => $request->overview,
                'aired' => $request->aired,
                'type' => $request->type,
                'status' => $request->status,
                'premiered' => $request->premiered,
                'broadcast' => $request->broadcast,
                'genres' => $request->genres ? implode(",", $request->genres) : '',
                'rating' => $request->rating,
                'popularity' => $request->popularity,
                'vote_average' => $request->vote_average,
                'trailer' => $request->trailer,
                'slug_flv' => $request->slug_flv,
                'slug_tio' => $request->slug_tio,
                'slug_jk' => $request->slug_jk,
                'slug_monos' => $request->slug_monos,
                'slug_fenix' => $request->slug_fenix
            ]);
            return redirect()->route('admin.animes.index')->with('success', 'Anime editado correctamente');
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Anime::findOrFail($id)->destroy($id);
            return redirect()->route('admin.animes.index')->with('success', 'Anime eliminado correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.animes.index')->with('error', $e->getMessage());
        }
    }

}
