<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Player;
use App\Models\Server;

class PlayerController extends Controller
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
        $episode = Episode::where('id',$request->episode_id)->first();
        if(!$episode){
            abort(404,'Not found Episode');
        }
        $players = Player::orderby('id','desc')->where('episode_id',$episode->id)->with('server')->get();
        $data = [
            'category_name' => 'players',
            'page_name' => 'list',
            'anime' => $anime,
            'episode' => $episode,
            'players' => $players
        ];
        return view('admin.players.list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $anime = Anime::where('id',$request->anime_id)->first();
        if(!$anime){
            abort(404,'Not found Anime');
        }
        $episode = Episode::where('id',$request->episode_id)->first();
        if(!$episode){
            abort(404,'Not found Episode');
        }
        $servers = Server::orderBy('id','asc')->get();
        $data = [
            'category_name' => 'players',
            'page_name' => 'create',
            'anime' => $anime,
            'episode' => $episode,
            'servers' => $servers
        ];
        return view('admin.players.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $anime_id, $episode_id)
    {
        try {
            Player::updateOrCreate([
                'server_id' => $request->server_id,
                'episode_id' => $episode_id,
                'code' => $request->code,
                'languaje' => $request->languaje
            ]);
            return redirect()->route('admin.animes.episodes.players.index',[$anime_id,$episode_id])->with('success', 'Reproductor añadido correctamente');
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
    public function edit(Request $request, $anime_id, $episode_id, $player_id)
    {
        $anime = Anime::where('id',$anime_id)->first();
        if(!$anime){
            abort(404,'Not found Anime');
        }
        $episode = Episode::where('id',$episode_id)->first();
        if(!$episode){
            abort(404,'Not found Episode');
        }
        $player = Player::where('id',$player_id)->first();
        if(!$player){
            abort(404,'Not found Player');
        }
        $servers = Server::orderBy('id','asc')->get();
        $data = [
            'category_name' => 'players',
            'page_name' => 'edit',
            'anime' => $anime,
            'episode' => $episode,
            'player' => $player,
            'servers' => $servers
        ];
        return view('admin.players.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $anime_id, $episode_id, $player_id)
    {
        try {
            Player::updateOrCreate(['id' => $player_id], [
                'server_id' => $request->server_id,
                'episode_id' => $episode_id,
                'code' => $request->code,
                'languaje' => $request->languaje
            ]);
            return redirect()->route('admin.animes.episodes.players.index',[$anime_id,$episode_id])->with('success', 'Reproductor editado correctamente');
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
    public function destroy($anime_id, $episode_id, $player_id)
    {
        try {
            Player::findOrFail($player_id)->destroy($player_id);
            return redirect()->route('admin.animes.episodes.players.index',[$anime_id,$episode_id])->with('success', 'Reproductor elimiando correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.animes.episodes.players.index',[$anime_id,$episode_id])->with('error', $e->getMessage());
        }
    }


    public function storePlayers(Request $request, $anime_id)
    {
        try {
            $quantity = ($request->last - $request->first) + 1;
            if ($request->first >= $request->last) {
                throw new \Exception('El capitulo inicial debe ser menor al final.');
            }
            $episodes = Episode::where('anime_id',$anime_id)->get();
            if ($quantity > count($episodes)) {
                throw new \Exception('Los reproductores generado exceden el numero de capitulos.');
            }
            $lista_player = array_map('trim', preg_split('/\R/', $request->list));
            if ($quantity > count($lista_player) || count($lista_player) > $quantity) {
                throw new \Exception('La lista de links no coincide con el número de reproductores a generar.');
            }
            $numerador = 0;
            for ($i = $request->first; $i <= $request->last; $i++) {
                $episode = Episode::where('anime_id',$anime_id)->where('number',$i)->first();
                if ($episode) {
                    $player = Player::where('episode_id',$episode->id)->where('server_id',$request->server_id)->where('languaje',$request->languaje)->first();
                    if ($player) {
                        Player::updateOrCreate(['id' => $player->id], [
                            'server_id' => $request->server_id,
                            'episode_id' => $episode->id,
                            'code' => $this->getCodeServer($lista_player[$numerador]),
                            'languaje' => $request->languaje
                        ]);
                    }else{
                        Player::updateOrCreate([
                            'server_id' => $request->server_id,
                            'episode_id' => $episode->id,
                            'code' => $this->getCodeServer($lista_player[$numerador]),
                            'languaje' => $request->languaje
                        ]);
                    }
                }
                $numerador++;
            }
            return redirect()->route('admin.animes.episodes.index',[$anime_id])->with('success', 'Reproductores añadidos correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->all())->with('error', $e->getMessage());
        } 
    }


    public function getCodeServer($url)
    {
        $parse = parse_url($url);
        $host = "";
        if(isset($parse["host"])){
            $host = $parse["host"];
        }
        switch ($host) {
            case 'www.mp4upload.com':
            case 'sbcloud1.com':
                $code = explode('/', $parse["path"]);
                $code = str_replace(array('embed-','.html'), '', $code[1]);
                break;
            case 'streamtape.com':
            case 'mixdrop.sx':
                $code = explode('/e/', $parse["path"]);
                if (count($code) == 1) {
                    $code = explode('/v/', $parse["path"]);
                }
                $code = explode('/', $code[1]);
                $code = $code[0];
                break;
            case 'www.yourupload.com':
                $code = explode('/embed/', $parse["path"]);
                $code = $code[1];
                break;
            case 'videos.sh':
                $code = explode('/', $parse["path"]);
                $code = $code[1];
                break;
            case 'evoload.io':
                $code = explode('/v/', $parse["path"]);
                $code = $code[1];
                break;
            default:
                $code = $url;
                break;
        }
        return $code;
    }

    public function allDeletePlayers(Request $request)
    {
        try {
            $anime = Anime::where('id',$request->anime_id)->first();
            if(!$anime){
                throw new Exception('Not found Anime.');
            }
            $episodes = Episode::where('anime_id',$anime->id)->get();
            if(count($episodes) > 0){
                foreach ($episodes as $episode) {
                    $players = Player::where('episode_id',$episode->id)->get();
                    if($players){
                        foreach($players as $player){
                            $player->where('languaje',$request->languaje)->where('episode_id',$episode->id)->delete();
                        }
                        
                    }
                }
            }else{
                throw new Exception('The number of episodes is not greater than 0.');
            }
            return redirect()->route('admin.animes.episodes.index',[$anime->id])->with('success', 'Reproductores '.($request->languaje == 0 ? 'Subtitulados': 'Latinos').' eliminados correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.animes.episodes.index',[$anime->id])->with('error', 'Hubo un error inesperado. -'.$e->getMessage());
        }
    }

}
