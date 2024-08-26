<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Server;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $anime = Anime::where('id',$request->anime_id)->first();
        if(!$anime){
            abort(404,'Not found Anime');
        }
        $episodes = Episode::orderby('number','desc')->where('anime_id',$anime->id)->get();
        $data = [
            'category_name' => $anime->title,
            'page_name' => 'list',
            'anime' => $anime,
            'episodes' => $episodes
        ];
        return view('admin.episodes.list')->with($data);
    }

    public function indexLatino(Request $request)
    {
        $anime = Anime::where('id',$request->anime_id)->first();
        if(!$anime){
            abort(404,'Not found Anime');
        }
        $episodes = Episode::select('episodes.*')
            ->orderby('number','desc')
            ->leftjoin('players','players.episode_id','episodes.id')
            ->groupby('number')
            ->where('languaje',1)
            ->where('anime_id',$anime->id)
            ->get();
        $data = [
            'category_name' => $anime->title,
            'page_name' => 'list',
            'anime' => $anime,
            'episodes' => $episodes
        ];
        return view('admin.episodes.listLatino')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($anime_id)
    {
        $anime = Anime::where('id',$anime_id)->first();
        if(!$anime){
            abort(404,"Not Found Anime");
        }
        $data = [
            'category_name' => 'episodes',
            'page_name' => 'create',
            'anime' => $anime
        ];
        return view('admin.episodes.create')->with($data);
    }

    public function generate($anime_id)
    {
        $anime = Anime::where('id',$anime_id)->first();
        if(!$anime){
            abort(404,"Not Found Anime");
        }
        $data = [
            'category_name' => 'episodes',
            'page_name' => 'generate',
            'anime' => $anime
        ];
        return view('admin.episodes.generate')->with($data);
    }

    public function generatePlayers($anime_id)
    {
        $anime = Anime::where('id',$anime_id)->first();
        if(!$anime){
            abort(404,"Not Found Anime");
        }
        $episodes = Episode::where('anime_id',$anime->id)->get();
        $servers = Server::orderBy('title','asc')->get();
        $data = [
            'category_name' => 'episodes',
            'page_name' => 'generate-players',
            'anime' => $anime,
            'episodes' => $episodes,
            'servers' => $servers
        ];
        return view('admin.episodes.generatePlayers')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $anime_id)
    {
        try {
            $anime = Anime::where('id',$anime_id)->first();
            if($request->quantity){
                $lastEpisode = Episode::orderby('id','desc')->where('anime_id',$anime_id)->first();
                $inicio = 1;
                $final = $request->quantity;
                if ($lastEpisode) {
                    $inicio = $lastEpisode->number+1;
                    $final =  $lastEpisode->number+$request->quantity;
                }
                for ($i=$inicio; $i <= $final; $i++) { 
                    Episode::updateOrCreate([
                        'anime_id' => $anime_id,
                        'number' => $i,
                        'created_at' => $anime->status == 0 ? '2020-01-01' : now()
                    ]);
                }
                return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Se generaron '.(($final-$inicio)+1).' episodios correctamente');
            }else{
                Episode::updateOrCreate([
                    'anime_id' => $anime_id,
                    'number' => $request->number
                ]);
                return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Episodio añadido correctamente');
            }
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
    public function edit($anime_id, $episode_id)
    {
        $anime = Anime::where('id',$anime_id)->first();
        if(!$anime){
            abort(404,"Not Found Anime");
        }
        $episode = Episode::where('id',$episode_id)->first();
        if(!$episode){
            abort(404,"Not Found Episode");
        }
        $data = [
            'category_name' => 'episodes',
            'page_name' => 'edit',
            'anime' => $anime,
            'episode' => $episode
        ];
        return view('admin.episodes.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $anime_id, $episode_id)
    {
        try {
            Episode::updateOrCreate(['id' => $episode_id], [
                'anime_id' => $anime_id,
                'number' => $request->number
            ]);
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Episodio editado correctamente');
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
    public function destroy($anime_id, $episode_id)
    {
        try {
            Episode::findOrFail($episode_id)->destroy($episode_id);
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Episodio eliminado correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('error', $e->getMessage());
        }
    }

    public function allDelete($anime_id)
    {
        try {
            $anime = Anime::where('id',$anime_id)->first();
            if(!$anime){
                abort(404,"Not Found Anime");
            }
            Episode::where('anime_id',$anime->id)->delete();
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Episodios eliminados correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('error', $e->getMessage());
        }
    }
    
}
