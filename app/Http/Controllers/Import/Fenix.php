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

class Fenix extends Controller
{

    public $base_url = "https://www.animefenix.com/";

    public function EpisodesImport(){
        try{
            $episodes = $this->getEpisodes();
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            if($episodes->status != 200)
                throw new Exception('No se pudo cargar la lista de episodios');
            else{
                foreach(array_reverse($episodes->data) as $episode){
                    $anime = Anime::where('slug_fenix',$episode->slug)->first();
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
            $container = $finder->query(".//div[(@class='capitulos-grid')]");
            $nodes = $finder->query(".//div[(@class='item')]", $container->item(0));
            $episodes = [];
            foreach($nodes as $node){
                $episodes[] = array(
                    'slug' => $this->getSlugAnime($finder->query(".//a", $node)->item(0)->getAttribute('href')),
                    'number' => $this->getNumberAnime($finder->query(".//a", $node)->item(0)->getAttribute('title'))
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
	        $response = $client->get($this->base_url.'ver/'.$slug);
            $data = $response->getBody()->getContents();
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $container = $finder->query("//ul[contains(@class, 'episode-page__servers-list')]");
            $nodes = $finder->query(".//a", $container->item(0));
            $players = explode("new Object();", $data);
            $players = explode("</script>", $players[1]);
            $players = $players[0];
            $videos = explode('= "', $players);
            $players = [];
            foreach($nodes as $key => $node){
                $players[] = array(
                    'title' => $this->getServerName($finder->query(".//span", $node)->item(1)->nodeValue),
                    'code' => $this->getUrl($finder->query(".//span", $node)->item(1)->nodeValue, $videos[$key+1])
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
        $url = explode("code=", $code);
        $url = explode("&", $url[1]);
        $url = $url[0];
        switch(strtolower($servidor)){
            case 'm':
                return 'https://mega.nz/embed#!v'.$url;
            case 'yourupload':
                return 'https://www.yourupload.com/embed/'.$url;
            case 'burst':
                return 'https://www.burstcloud.co/embed/'.$url;
            case 'ru':
                return 'https://ok.ru/videoembed/'.$url;
            case 'fireload':
                return 'https://'.$url;
            case 'fembed':
                return 'https://www.fembed.com/v/'.$url;
            case 'mp4upload':
                return 'https://www.mp4upload.com/embed-'.$url.'.html';
            case 'sendvid':
                return 'https://sendvid.com/embed/'.$url;
            default :
                return $url;
        }
    }
    
    public function getServerName($name){
        switch(strtolower($name)){
            case 'm':
                return 'F_Mega';
            case 'yourupload':
                return 'F_YourUpload';
            case 'burst':
                return 'F_Burst';
            case 'ru':
                return 'F_Okru';
            case 'fireload':
                return 'F_Firelodad';
            case 'fembed':
                return 'F_Fembed';
            case 'mp4upload':
                return 'F_Mp4upload';
            case 'sendvid':
                return 'F_Sendvid';
            default :
                return $name;
        }
    }
    
    public function getNumberAnime($name) {
        $anime = explode(' ', $name);
        $last = array_pop($anime);
        return $last;
    }
    
    public function getSlugAnime($name) {
        $name = str_replace('https://www.animefenix.com/ver/','',$name);
        $anime = explode('-', $name);
        $last = array_pop($anime);
        $anime = implode('-', $anime);
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
            $container = $finder->query("//ul[contains(@class, 'anime-page__episode-list')]");
            $nodes = $finder->query(".//a", $container->item(0));
            $episodes = [];
            $perPage = 10;
            $totalEpis = count($nodes);
            $totalPages = ceil(count($nodes) / $perPage);
            $pages = [];
            for($i = 1; $i <= $totalPages; $i++){
                $pages[] = array(
                    'init' => $i == 1 ? 1 : ((($i - 1) * $perPage) + 1),
                    'fin' => $i * $perPage >= $totalEpis ? $totalEpis : $i * $perPage
                );
            };
            return array(
                'total' => count($nodes),
                'perPage' => $perPage,
                'numPages' => ceil(count($nodes) / $perPage),
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

}
