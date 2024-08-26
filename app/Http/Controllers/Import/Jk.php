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

class Jk extends Controller
{

    public $base_url = "https://jkanime.net/";

    public function EpisodesImport(){
        try{
            $episodes = $this->getEpisodes();
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            if($episodes->status != 200)
                throw new Exception('No se pudo cargar la lista de episodios');
            else{
                foreach(array_reverse($episodes->data) as $episode){
                    $anime = Anime::where('slug_jk',$episode->slug)->first();
                    if($anime){
                        if(Episode::where('number',$episode->number)->where('anime_id',$anime->id)->exists() === FALSE){
                            $episode_store = Episode::updateOrCreate(['anime_id' => $anime->id,'number' => $episode->number]);
                            $repros = $this->createPlayersEpisode($episode_store->id,$episode->slug.'/'.$episode->number);
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
            $container = $finder->query(".//div[(@class='listadoanime-home')]");
            $nodes = $finder->query(".//a[(@class='bloqq')]", $container->item(0));
            $episodes = [];
            foreach($nodes as $node){
                $episodes[] = array(
                    'slug' => $this->getSlugAnime($node->getAttribute('href')),
                    'number' => $this->getNumberAnime($node->getAttribute('href'))
                );
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
	        $response = $client->get($this->base_url.$slug);
            $data = $response->getBody()->getContents();
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $container = $finder->query("//div[contains(@class, 'bg-servers')]");
            $nodes = $finder->query(".//a", $container->item(0));
            $players = explode("var video = [];", $data);
            $players = explode("var", $players[1]);
            $players = $players[0];
            $videos = explode("= '", $players);
            $players = [];
            foreach($nodes as $key => $node){
                $players[] = array(
                    'title' => $this->getServerName($node->nodeValue),
                    'code' => $this->getUrl($node->nodeValue, $videos[$key+1])
                );
            }
            return array(
                'data' => $players,
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

    public function getUrl($servidor, $code){
        $url = explode('src="', $code);
        $url = explode('"', $url[1]);
        $url = $url[0];
        $url = explode('?u=',$url);
        if(count($url) > 1){
            $url = $url[1];
        }else{
            $url = $url[0];
        }
        $url = urldecode($url);
        switch(strtolower($servidor)){
            case 'xtreme s':
                return 'https://jkanime.net/jk.php?u='.$url;
            case 'fembed':
                return 'https://www.fembed.com/v/'.$url;
            case 'okru':
                return 'https://ok.ru/videoembed/'.$url;
            case 'mixdrop':
                return 'https://mixdrop.co/e/'.$url;
            default:
                return $url;
        }
    }
    
    public function getServerName($name){
        switch(strtolower($name)){
            case 'desu':
                return 'J_Desu';
            case 'mega':
                return 'J_Mega';
            case 'xtreme s':
                return 'J_Xtreme';
            case 'xtreme s':
                return 'J_Xtreme';
            case 'okru':
                return 'J_Okru';
            case 'fembed':
                return 'J_Fembed';
            case 'mixdrop':
                return 'J_Mixdrop';
            default :
                return $name;
        }
    }
    
    public function getNumberAnime($name) {
        $number = explode('/', $name);
        $last = array_pop($number);
        $last = array_pop($number);
        return $last;
    }
    
    public function getSlugAnime($name) {
        $name = str_replace('https://jkanime.net/','',$name);
        $anime = explode('/', $name);
        $anime = $anime[0];
        return $anime;
    }

    //NEW IMPORT

    public function getAnime(Request $request)
    {
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.$request->slug);
            $data = $response->getBody()->getContents();
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $container = $finder->query("//div[contains(@class, 'anime__pagination')]");
            $nodes = $finder->query("//a[contains(@class, 'numbers')]", $container->item(0));
            $item = $nodes->item($nodes->length - 1)->nodeValue;
            $item = explode(' - ',$item);
            $item = $item[1];
            $episodes = [];
            $perPage = 10;
            $totalEpis = ceil($item);
            $totalPages = ceil($totalEpis / $perPage);
            $pages = [];
            for($i = 1; $i <= $totalPages; $i++){
                $pages[] = array(
                    'init' => $i == 1 ? 1 : ((($i - 1) * $perPage) + 1),
                    'fin' => $i * $perPage >= $totalEpis ? $totalEpis : $i * $perPage
                );
            };
            return array(
                'total' => $totalEpis,
                'perPage' => $perPage,
                'numPages' => ceil($totalEpis / $perPage),
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
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'/'.$i);
                        $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                    }else{
                        $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'/'.$i);
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
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'/'.$i);
                            $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                        }else{
                            $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'/'.$i);
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

}
