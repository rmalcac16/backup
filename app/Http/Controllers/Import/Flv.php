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

class Flv extends Controller
{

    public $base_url = "https://www3.animeflv.net/";

    public function EpisodeImport(){
        try{
            $episodes = array_reverse($this->getEpisodes());
            $episodes = json_decode(json_encode($episodes), FALSE);
            $import = [];
            dd($episodes);
            foreach($episodes as $episode){
                $slug_anime = explode('-', str_replace('/ver/','',$episode->slug));
                $last = array_pop($slug_anime);
                $slug_anime = array(implode('-', $slug_anime), $last);
                $anime = Anime::where('slug_flv',$slug_anime[0])->first();
                if($anime){
                    if(Episode::where('number',$episode->number)->where('anime_id',$anime->id)->exists() === FALSE){
                        Episode::updateOrCreate([
                            'anime_id' => $anime->id,
                            'number' => $episode->number,
                        ]);
                        $import[] = array(
                            'anime' => $anime->name,
                            'episode' => $episode->number
                        );
                        $episodeDB = Episode::where('number',$episode->number)->where('anime_id',$anime->id)->first();
                        $this->createPlayersEpisode($episodeDB->id,$episode->slug);
                    }
                }
            }
            return array(
                'data' => $import
            );
        }catch(Exception $e){
	        return $e->getMessage();
        } 
    }

    public function createPlayersEpisode($id,$slug){
        foreach($this->getVideos($slug) as $video){
            $server = Server::where('title',$video->title)->first();
            if($server){
                Player::updateOrCreate([
                    'server_id' => $server->id,
                    'episode_id' => $id,
                    'code' => $video->code,
                    'languaje' => 0
                ]);
            }
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
            $nodes = $finder->query("//li[contains(@class, 'Episode')]");
            $episodes = [];
            foreach($nodes as $item){
                if ($item->getElementsByTagName("h2")->item(0)){
                    $episodes[] = array(
                        'slug' => $item->getElementsByTagName("a")->item(0)->getAttribute('href'),
                        'name' => $item->getElementsByTagName("h2")->item(0)->nodeValue,
                        'number' => $item->getElementsByTagName("p")->item(0)->lastChild->nodeValue
                    );
                }
            }
            return $episodes;
        }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
        } 
    }

    public function getVideos($slug){
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.$slug);
            $data = $response->getBody()->getContents();
            $videos = explode('videos =',$data);
            $videos = explode(';',$videos[1]);
            $videos = json_decode($videos[0]);
            return $videos->SUB;
        }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
        } 
    }

    //NEW IMPORT

    public function getAnime(Request $request){
	    try{
	       return $this->getEpisodesAnimeSlug($request->slug,$request->id);
        }catch(Exception $e){
            return array(
                'msg' => $e->getMessage(),
                'code' => $e->getCode()
            );
        } 
    }

    public function getEpisodesAnimeSlug($slug,$id)
    {
        $client = new Client(['timeout' => 2 ]);
        try {
            $response = $client->ge($this->base_url.'/anime/'.$slug);
            $data = $response->getBody()->getContents();
            $episodes = explode("var episodes = [",$data);
            $episodes = explode("];",$episodes[1]);
            $episodes = $episodes[0];
            $episodes = explode(",[",$episodes);
            $episodes = str_replace(array("[","]"),"",$episodes);
            $items = [];
            foreach(array_reverse($episodes) as $episode){
                $items[] = array(
                    'number' => explode(',',$episode)[0],
                    'slug' => $slug.'-'.explode(',',$episode)[0],
                    'anime_id' => $id
                );
            }
            return $items;
        } catch (Exception $e) {
            return array(
                'msg' => $e->getMessage()
            );
        }
    }

    public function importEpisodes(Request $request){
        try {
            $listEpisodes = $this->getEpisodesAnimeSlug($request->slug,$request->id);
            $listEpisodes = json_encode($listEpisodes);
            $listEpisodes = json_decode($listEpisodes);
            if(isset($listEpisodes->msg)){
                throw new Exception("Error Processing Request", 404);
            }
            $imports = [];
            foreach ($listEpisodes as $episode) {
                $episodeNew = $this->createEpisode($episode);
                $imports[] = $episodeNew;
            }
            return array(
                'success' => true,
                'data' => $imports
            );
        } catch (Exception $e) {
            return array(
                'msg' => $e->getMessage(),
                'code' => $e->getCode()
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
                    $success = $this->createNewPlayers($newEpisodeDB->id,$episode->slug);
                }
                return $newEpisodeDB;
            }else{
                $epsUp = Episode::where('anime_id',$episode->anime_id)->where('number',$episode->number)->first();
                $success = $this->createNewPlayers($epsUp->id,$episode->slug);
                return $epsUp;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function createNewPlayers($episode_id,$slug)
    {
        try {
            foreach($this->getEpisodesVideo($slug) as $video){
                $server = Server::where('title',$video->title)->first();
                if($server){
                    Player::updateOrCreate([
                        'server_id' => $server->id,
                        'episode_id' => $episode_id,
                        'code' => $video->code,
                        'languaje' => 0
                    ]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createNewEpisode($anime_id,$number)
    {
        return Episode::updateOrCreate([
            'anime_id' => $anime_id,
            'number' => $number,
            'created_at' => '2021-01-01'
        ]);
    }

    public function verifyEpisodeExists($anime_id,$number)
    {
        return Episode::where('anime_id',$anime_id)->where('number',$number)->exists();
    }

    public function getEpisodesVideo($slug){
        $client = new Client(['timeout' => 2 ]);
	    try{
	        $response = $client->get($this->base_url.'/ver/'.$slug);
            $data = $response->getBody()->getContents();
            $videos = explode('videos =',$data);
            $videos = explode(';',$videos[1]);
            $videos = json_decode($videos[0]);
            return $videos->SUB;
        }catch(Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
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
