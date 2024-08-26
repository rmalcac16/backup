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

class Monos extends Controller
{

    public $base_url = "https://monoschinos2.com/";

    public function EpisodesImport(){
        try{
            $episodes = $this->getEpisodes();
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            if($episodes->status != 200)
                throw new Exception('No se pudo cargar la lista de episodios');
            else{
                foreach(array_reverse($episodes->data) as $episode){
                    $anime = Anime::where('slug_flv',$episode->slug)->first();
                    if($anime){
                        if(Episode::where('number',$episode->number)->where('anime_id',$anime->id)->exists() === FALSE){
                            $episode_store = Episode::updateOrCreate(['anime_id' => $anime->id,'number' => $episode->number]);
                            $repros = $this->createPlayersEpisode($episode_store->id,$episode->slug.'-episodio-'.$episode->number);
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
            $nodes = $finder->query("//article[contains(@class, 'col-6')]");
            $episodes = [];
            foreach($nodes as $item){
                if ($item->getElementsByTagName("h2")->item(0)){
                    $episodes[] = array(
                        'slug' => $this->getSlugAnime($finder->query(".//a",$item)->item(0)->getAttribute('href')),
                        'number' => $this->getNumberAnime(trim($finder->query(".//span[contains(@class, 'episode')]",$item)->item(0)->nodeValue))
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
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $nodes = $finder->query("//li[contains(@class, 'Button')]");
            $nodes2 = $finder->query("//div[contains(@class, 'embed-responsive')]");
            $players = [];
            foreach($nodes as $key => $item){
                $players[] = array(
                    'title' => $this->getServerName($item->getAttribute('title')),
                    'code' => $this->videoDecode($key == 0 ? $nodes2[$key]->getElementsByTagName('iframe')->item(0)->getAttribute('src') : $this->getUrl($nodes2[$key]->nodeValue))
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

    public function videoDecode($url){
        $url_decode = str_replace('https://monoschinos2.com/reproductor?url=','',$url);
        $url_decode = urldecode($url_decode);
        $url_decode = explode('&id',$url_decode);
        $url_decode = $url_decode[0];
        return $url_decode; 
    }

    public function getUrl($name){
        if(count(explode('src="',$name)) == 1){
            $name = explode('src=&quot;',$name);
            $name = explode('&quot;',$name[1]);
            $name= $name[0];
        }else if(count(explode('src="',$name)) == 2){
            $name = explode('src="',$name);
            $name = explode('"',$name[1]);
            $name= $name[0];
        }
        return $name;
    }
    
    public function getServerName($name){
        switch(strtolower($name)){
            case 'fembed':
                return 'M_Fembed';
            case 'streamtape':
                return 'M_Streamtape';
            case 'uqload':
                return 'M_Uqload';
            case 'playersb':
                return 'M_PlayerSB';
            case 'videobin':
                return 'M_Videobin';
            case 'mp4upload':
                return 'M_Mp4upload';
            case 'ok':
                return 'M_Okru';
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
        $name = str_replace('https://monoschinos2.com/ver/','',$name);
        $anime = explode('-', $name);
        $last = array_pop($anime);
        $last_two = array_pop($anime);
        $anime = implode('-', $anime);
        return $anime;
    }

    //NEW IMPORT

    public function getAnime(Request $request)
    {
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.'anime/'.$request->slug.'-sub-espanol');
            $data = $response->getBody()->getContents();
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
            $finder = new DomXPath($dom);
            $nodes = $finder->query("//a[contains(@class, 'item')]");
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
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-episodio-'.$i);
                        $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                    }else{
                        $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                        $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-episodio-'.$i);
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
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-episodio-'.$i);
                            $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                        }else{
                            $episode_store = Episode::where('anime_id',$anime->id)->where('number',$i)->first();
                            $repros = $this->createPlayersEpisode($episode_store->id,$request->slug.'-episodio-'.$i);
                            $import[] = array('anime' => $anime->name,'episode'=> $i,'players' => $repros);
                        }
                    }
                }
            }
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
