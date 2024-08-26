<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use DomDocument;
use DomXPath;
use GuzzleHttp\Client;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Server;
use App\Models\Player;

class Tio extends Controller
{

    public $base_url = "https://tioanime.com/";

    public function EpisodesImport(){
        try{
            $episodes = $this->getEpisodes();
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            if($episodes->status != 200)
                throw new Exception('No se pudo cargar la lista de episodios');
            else{
                foreach(array_reverse($episodes->data) as $episode){
                    $anime = Anime::where('slug_tio',$episode->slug)->first();
                    if($anime){
                        if(Episode::where('number',$episode->number)->where('anime_id',$anime->id)->exists() === FALSE){
                            $episode_store = Episode::updateOrCreate(['anime_id' => $anime->id,'number' => $episode->number]);
                            $repros = $this->createPlayersEpisode($episode_store->id,$episode->slug.'-'.$episode->number);
                            $import[] = array('anime' => $anime->name,'episode'=> $episode->number,'players' => $repros);
                        }
                    }
                }
            }
            return array(
                'status' => 200,
                'data' => $import
            );
        }catch(Exception $e){
	        return array(
                'status' => 404,
                'data' => [],
                'message' => $e->getMessage()." ".$e->getLine()
            );
        }
    }

    public function createPlayersEpisode($id,$slug){
        try{
            $videos = $this->getVideos($slug);
            $videos = json_decode(json_encode($videos), FALSE);
            if($videos->status != 200)
                throw new Exception('No se pudo cargar los reproductores');
            else{
                $response = [];
                foreach($videos->data as $video){
                    $server = Server::where('title',$video->title)->first();
                    if($server){
                        $videoExists = Player::where('server_id',$server->id)->where('episode_id',$id)->where('languaje',0)->first();
                        if($videoExists){
                            $response[] = Player::where('id', $videoExists->id)->update(['server_id' => $server->id,'episode_id' => $id,'code' => $video->code,'languaje' => 0]);
                        }else{
                            $response[] = Player::create(['server_id' => $server->id,'episode_id' => $id,'code' => $video->code,'languaje' => 0]);
                        }
                            
                    }
                }
                return array(
                    'data' => $response
                );
            }
        }catch(Exception $e){
	        return array(
                'data' => [],
                'message' => $e->getMessage()." ".$e->getLine()
            );
        }
    }

    public function getEpisodes(){
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url);
            $data = $response->getBody()->getContents();
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $nodes = $finder->query("//article[contains(@class, 'episode')]");
            $episodes = [];
            foreach($nodes as $item){
                if ($item->getElementsByTagName("h3")->item(0)){
                    $episodes[] = array(
                        'slug' => $this->getSlugAnime($finder->query(".//a",$item)->item(0)->getAttribute('href')),
                        'number' => $this->getNumberAnime(trim($finder->query(".//h3[contains(@class, 'title')]",$item)->item(0)->nodeValue))
                    );
                }
            }
            return array(
                'data' => $episodes,
                'status' => 200
            );
        }catch(Exception $e){
	        return array(
                'status' => 404,
                'message' => $e->getMessage()." ".$e->getLine()
            );
        } 
    }

    public function getVideos($slug){
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.'ver/'.$slug);
            $data = $response->getBody()->getContents();
            $videos = explode('videos =',$data);
            $videos = explode(';',$videos[1]);
            $videos = json_decode($videos[0]);
            $videos_c = [];
            foreach($videos as $video){
                $videos_c[] = array(
                    'title' => $this->getServerName($video[0]),
                    'code' => $video[1]
                );
            }
            return array(
                'data' => $videos_c,
                'status' => 200
            );
        }catch(Exception $e){
	        return array(
                'data' => [],
                'status' => 404,
                'message' => $e->getMessage()." ".$e->getLine()
            );
        } 
    }
    
    public function getServerName($name){
        switch(strtolower($name)){
            case 'fembed':
                return 'none';
            case 'umi':
            case 'streamium':
                return 'none';				
            case 'mega':
                return 'none';
            case 'okru':
                return 'none';
            default :
                return 'none';
        }
    }
    
    public function getNumberAnime($name) {
        $anime = explode(' ', $name);
        $last = array_pop($anime);
        return $last;
    }
    
    public function getSlugAnime($name) {
        $name = str_replace('/ver/','',$name);
        $anime = explode('-', $name);
        $last = array_pop($anime);
        $anime = array(implode('-', $anime), $last);
        return $anime[0];
    }

    //NEW IMPORT

    public function getAnime(Request $request)
    {
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.'/anime/'.$request->slug);
            $data = $response->getBody()->getContents();
            $list = explode("var episodes = [",$data);
            $list = explode("];",$list[1]);
            $list = $list[0];
            $list = explode(",",$list);
            $perPage = 10;
            $totalEpis = count($list);
            $totalPages = ceil(count($list) / $perPage);
            $pages = [];
            for($i = 1; $i <= $totalPages; $i++){
                $pages[] = array(
                    'init' => $i == 1 ? 1 : ((($i - 1) * 10) + 1),
                    'fin' => $i * $perPage >= $totalEpis ? $totalEpis : $i * $perPage
                );
            };
            return array(
                'total' => count($list),
                'perPage' => $perPage,
                'numPages' => ceil(count($list) / $perPage),
                'pages' => $pages,
                'slug' => $request->slug,
                'idAnime' => $request->id,
                'status' => 200
            );
        }catch(Exception $e){
	        return array(
                'status' => 404,
                'message' => $e->getMessage()." ".$e->getLine()
            );
        }
    }

    public function importarEpisodesPerPage(Request $request){
        try{
            $anime = Anime::where('id',$request->id)->first();
            $import = [];
            if($anime){
                for($i = $request->inicio; $i <= $request->fin; $i++){
                    if(Episode::where('number',$i)->where('anime_id',$anime->id)->exists() === FALSE){
                        $episode_store = Episode::updateOrCreate(['anime_id' => $anime->id,'number' => $i,'created_at' => '2021-01-01']);
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-'.$i);
                        $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                    }else{
                        $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-'.$i);
                        $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                    }
                }
            }
            return array(
                'status' => 200,
                'data' => $import
            );
        }catch(Exception $e){
	        return array(
                'status' => 404,
                'data' => [],
                'message' => $e->getMessage()." ".$e->getLine()
            );
        }
    }

    public function importEpisodes(Request $request){
        try{
            $episodes = $this->getAnime($request);
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            if($episodes->status != 200)
                throw new Exception('No se pudo cargar la lista de episodios');
            else{
                $anime = Anime::where('id',$request->id)->first();
                if($anime){
                    for($i = 1; $i <= $episodes->total; $i++){
                        if(Episode::where('number',$i)->where('anime_id',$anime->id)->exists() === FALSE){
                            $episode_store = Episode::updateOrCreate(['anime_id' => $anime->id,'number' => $i,'created_at' => '2021-01-01']);
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-'.$i);
                            $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                        }else{
                            $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-'.$i);
                            $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                        }
                    }
                }
            }
            return array(
                'status' => 200,
                'data' => $import
            );
        }catch(Exception $e){
	        return array(
                'status' => 404,
                'data' => [],
                'message' => $e->getMessage()." ".$e->getLine()
            );
        }
    }

    public function createEpisode($episode)
    {
        try {
            $verify = $this->verifyEpisodeExists($episode->anime_id,$episode->number);
            if(!$verify){
                $newEpisodeDB = $this->createNewEpisode($episode->anime_id,$episode->number);
                if($newEpisodeDB){
                    $success = $this->createPlayersEpisode($newEpisodeDB->id,$episode->slug);
                }
                return $newEpisodeDB;
            }else{
                $epsUp = Episode::where('anime_id',$episode->anime_id)->where('number',$episode->number)->first();
                $success = $this->createPlayersEpisode($epsUp->id,$episode->slug);
                return $epsUp;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function objectify($assoc) {
        $new_array = array();
        foreach ($assoc as $to_obj){
            $new_array[] = (object)$to_obj;
        }
        return $new_array;
    }

    function getLinksByServer($server, $anime, $inicio, $fin) {
        try {
            // Convertir los valores de inicio y fin a enteros
            $start = intval($inicio);
            $end = intval($fin);
    
            // Validar que los valores de inicio y fin sean números válidos
            if (!is_numeric($start) || !is_numeric($end) || $start > $end) {
                return response()->json(['error' => 'Invalid start and end values'], 400);
            }
    
            $links = $this->getLinksInRange($server, $anime, $start, $end);
            return response()->json($links);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // Función para obtener los enlaces del servidor voe.sx en un rango específico
    function getLinksInRange($server, $anime, $start, $end) {
        $links_all = [];
        $client = new Client();
        for ($i = $start; $i <= $end; $i++) {
            $url = "https://tioanime.com/ver/$anime-$i";
            $links = $this->getLinksFromURL($server, $client, $url);
            if ($links && isset($links[0])) {
                if (strtolower($server) === "voe") {
                    $links[0] = $links[0];
                }
            } else {
                throw new Exception("No se ha encontrado links para el servidor", 2);
            }
            $links_all = array_merge($links_all, $links);
        }
        return $links_all;
    }

    // Función para obtener los enlaces del servidor voe.sx desde una URL
    function getLinksFromURL($server, $client, $url) {
        try {
            $response = $client->get($url);
            $body = (string) $response->getBody();
            $start = strpos($body, 'var videos = [[');
            $end = strpos($body, ']];');
            if ($start === false || $end === false) {
                return [];
            }
            $linksString = substr($body, $start, $end - $start + 2);
            $linksArrayString = str_replace('var videos = ', '', $linksString);
            $linksArray = json_decode($linksArrayString, true);
            $links = array_filter($linksArray, function ($link) use ($server) {
                return strtolower($link[0]) == $server;
            });
            return array_column($links, 1);
        } catch (Exception $e) {
            return "The server link was not found in this episode.";
        }
    
    }

}
