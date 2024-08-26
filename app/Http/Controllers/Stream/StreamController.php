<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\Player;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\JavascriptUnpacker;
use Google\Cloud\Storage\StorageClient;

class StreamController extends Controller
{
	public $url;

	public function getStream(Request $request)
	{
        try {

            $referer = $_SERVER['HTTP_REFERER'] ?? null;
            $parse = parse_url($referer);
            if($parse['host'] != 'www.animelatinohd.com')
                throw new Exception('Sin Acceso');

            $player = Player::where('id',$request->id)->with('server')->first();

            if(!$player)
                throw new Exception('No encontrado');

            switch ($player->server->type) {
                case '0':
                    $data = ['player' => $player];
                    return view('stream.video')->with($data);
                    break;
                case '1':
                    return redirect($player->code);
                    break;
                case '2':
                    if(strtolower($player->server->title) ==  "gamma")
                        {
                            $idVoe = explode("/",$player->code);
                            $idVoe = $idVoe[4];
                            $player->code = $player->server->embed."e/".$idVoe;
                        }
                    return redirect($player->code);
                    break;
                default:
                    return redirect($player->code);
                    break;
            }
            
        } catch (\Exception $e) {
            abort(403);
        }
		
	}


    public function video(Request $request)
	{
        try {
            $player = Player::where('id',$request->id)->with('server')->first();
            if(!$player)
                throw new Exception('No encontrado');

            switch ($player->server->type) {
                case '0':
                    $data = ['player' => $player];
                    return view('stream.video')->with($data);
                    break;
                case '1':
                    return redirect($player->code);
                    break;
                case '2':
                    if(strtolower($player->server->title) ==  "gamma")
                        {
                            $idVoe = explode("/",$player->code);
                            $idVoe = $idVoe[4];
                            $player->code = $player->server->embed."e/".$idVoe;
                        }
                    dd($player->code);    
                    return redirect($player->code);
                    break;
                default:
                    return redirect($player->code);
                    break;
            }
            
        } catch (\Exception $e) {
            abort(403);
        }
		
	}

    public function getStream2(Request $request)
	{
        try {

            // // $referer = $_SERVER['HTTP_REFERER'] ?? null;
            // // $parse = parse_url($referer);
            // // if($parse['host'] != 'www.animelatinohd.com')
            // //     throw new Exception('Sin Acceso');

            // $player = Player::where('id',$request->id)->with('server')->first();

            // if(!$player)
            //     throw new Exception('No encontrado');

            // switch ($player->server->type) {
            //     case '0':
            //         $data = ['player' => $player];
            //         return view('stream.video')->with($data);
            //         break;
            //     case '1':
            //         return redirect($player->code);
            //         break;
            //     case '2':
            //         if(strtolower($player->server->title) ==  "gamma")
            //             {
            //                 $idVoe = explode("/",$player->code);
            //                 $idVoe = $idVoe[4];
            //                 $player->code = $player->server->embed."e/".$idVoe;
            //             }
            //         return redirect($player->code);
            //         break;
            //     default:
            //         return redirect($player->code);
            //         break;
            // }

            $data = [
                'url' => 'animes'
            ];

            return view('stream.iframe')->with($data);
            
        } catch (\Exception $e) {
            abort(403);
        }

	}
	

	public function degooStream(Request $request){
		$parse_url = parse_url($request->get('url'));
        $id = str_replace(array('/share/','/files/'),'',$parse_url['path']);;
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->post('https://rest-api.degoo.com/shared',
                ['body' => json_encode(
                    [
                        'HashValue' => $id,
                        'Limit' => 1,
                        'FileID' => null,
                        'JWT' => null
                    ]
                )]
            );
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if($data->Items && $data->Items[0]){
                return $data->Items[0]->URL;
            }else{
                throw new Exception('Not Found Video Url'); 
            }
        }catch(Exception $e){
	        return $e->getMessage();
        }
	}
	
	public function getVideoMp4(Request $request){
	    try{
	        $player = Player::where('id',$request->id)->with('server')->first();
	        $request->s = $player->server->title;
	        $request->code = Crypt::encryptString($player->code);
	        return $this->generateVideo($request);
	    }catch(Exception $e){
	        return $e->getMessage();
	    }
	}

	public function generateVideo(Request $request)
	{
	    switch (strtolower($request->s)) {
            case 'beta':
                $videoLink = $this->betaLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink);
                break;
            case 'gphotos':
                $videoSource = $this->gphotosLink(Crypt::decryptString($request->code));
                break;
            case 'degoo':
                $videoLink = $this->degooLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink);
                break;
            case 'archive':
                $videoLink = $this->archiveLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink);
                break;
            case 'videos':
                $videoLink = $this->VideosLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink,'hls');
                break;
            case 'zplayer':
                $videoLink = $this->ZplayerLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink,'hls');
                break;
            case 'fireload':
                $videoLink = $this->FireLink(Crypt::decryptString($request->code));
                break;
            case 'solidfiles':
                $videoLink = $this->SolidLink(Crypt::decryptString($request->code));
                $videoSource = $this->videoToSource($videoLink);
                break;
            default:
               $videoLink = Crypt::decryptString($request->code);
               $videoSource = $this->videoToSource($videoLink);
        }
        return $videoSource;
	}
	
	public function videoToSource($url,$type = "video/mp4"){
	    return array(array(
	        "type" => $type,
	        "label" => "720p",
	        "file" => $url
	   ));
	}
	
	public function archiveLink($url){
	    $parse = parse_url($url);
	    $path = $parse['path'];
	    $path = explode('/',$path);
	    $bucket = $path[2];
	    $file = $path[3];
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->get('https://archive.org/metadata/'.$bucket);
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            return 'https://'.$data->server.$data->dir.'/'.$file;
        }catch(\Exception $e){
	        return $e->getMessage();
        }
	}

    public function FireLink(Request $request){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->post('https://www.fireload.com/api/v2/authorize',[
                'form_params' => [
                    'key1' => 'p5w9UUKl0KDFXikbhcwkrhNtLuw5FavMS1vhafnlBIv6YBSFZg9U8b9gY9FgKY3f',
                    'key2' => 'yvgSZjc6BzWsZBVG3uY1jTH03U1ckJXxiwtLFJ90TsW2Be0ul57RF3dHtjKs1vvB'
                ]
            ]);
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if($data->_status == 'success'){
                $responseVideo = $client->post('https://www.fireload.com/api/v2/file/download',[
                    'form_params' => [
                        'access_token' => $data->data->access_token,
                        'account_id' => $data->data->account_id,
                        'file_id' => $request->id
                    ]
                ]);
                $dataVideo = $responseVideo->getBody()->getContents();
                $dataVideo = json_decode($dataVideo);
                if($dataVideo->_status == "success"){
                    return $dataVideo->data->download_url;
                }
            }
        }catch(\Exception $e){
	        return $e->getMessage();
        }
	}
    
    public function FireLinks(Request $request){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->post('https://www.fireload.com/api/v2/authorize',[
                'form_params' => [
                    'key1' => 'p5w9UUKl0KDFXikbhcwkrhNtLuw5FavMS1vhafnlBIv6YBSFZg9U8b9gY9FgKY3f',
                    'key2' => 'yvgSZjc6BzWsZBVG3uY1jTH03U1ckJXxiwtLFJ90TsW2Be0ul57RF3dHtjKs1vvB'
                ]
            ]);
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if($data->_status == 'success'){
                $responseFolders = $client->post('https://www.fireload.com/api/v2/folder/listing',[
                    'form_params' => [
                        'access_token' => $data->data->access_token,
                        'account_id' => $data->data->account_id,
                        'parent_folder_id' => $request->id
                    ]
                ]);
                $dataFolders = $responseFolders->getBody()->getContents();
                $dataFolders = json_decode($dataFolders);
                if($dataFolders->_status == 'success'){
                    foreach($dataFolders->data->folders as $folder){
                        echo $folder->folderName.' - '.$folder->id;
                    }
                    if($dataFolders->data->files){
                        foreach($dataFolders->data->files as $file){
                            echo $file->id.'<br/>';
                        }
                    }
                }
            }
        }catch(\Exception $e){
	        return $e->getMessage();
        }
	}
	
	public function betaLink($url){
	    $parse = parse_url($url);
	    $path = explode('/',$parse['path']);
	    $path = array_splice($path,1,99);
	    $bucket =  $path[0];
	    $object = array_splice($path,1,99);
	    $object = implode('%2F',$object);
        $userProject = ENV("CLOUD_PROJECT_ID");
	    $link = 'https://content-storage.googleapis.com/download/storage/v1/b/'.$bucket.'/o/'.$object.'?alt=media&userProject='.$userProject;
	    $client = new \Google\Client();
	    $client->setAuthConfig(public_path('user.json'));
	    $client->addScope('https://www.googleapis.com/auth/devstorage.read_only');
	    $client->setAccessType('offline');
	    $httpClient = $client->authorize();
        
	    try{
	        $response = $httpClient->get($link,[
	            'timeout' => 1,
	            'on_stats' => function (TransferStats $stats) use (&$url) {
                    $this->url = $stats->getEffectiveUri();
	            },
	        ]);
	        return $this->url->__toString();
	    }catch(\Exception $e){
	        return $this->url ? $this->url->__toString() : 'Error';
	    }
	}
	
	public function betaLinks(Request $request){
	    $bucket = "animelhd";
	    $userProject = ENV("CLOUD_PROJECT_ID");
	    $link = 'https://storage.googleapis.com/storage/v1/b/'.$bucket.'/o?startOffset='.$request->lang.'%2F'.$request->bucket.'&userProject='.$userProject;
	    $client = new \Google\Client();
	    $client->setAuthConfig(public_path('user.json'));
	    $client->addScope('https://www.googleapis.com/auth/devstorage.read_only');
	    $client->setAccessType('offline');
	    $httpClient = $client->authorize();
	    try{
	        $response = $httpClient->get($link);
	        $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if(isset($data->items)){
                foreach($data->items as $item){
                    if (str_contains($item->name, $request->bucket)) {
                        echo 'https://storage.cloud.google.com/'.$bucket.'/'.$item->name.'<br>';
                    }
                }
            }
	    }catch(Exception $e){
	        return 'Error';
	    }
	}

    public function wasabiLinks(Request $request){
	    $bucket = "animelhd";
	    $userProject = ENV("CLOUD_PROJECT_ID");
	    $link = 'https://storage.googleapis.com/storage/v1/b/'.$bucket.'/o?startOffset='.$request->lang.'%2F'.$request->bucket.'&userProject='.$userProject;
	    $client = new \Google\Client();
	    $client->setAuthConfig(public_path('user.json'));
	    $client->addScope('https://www.googleapis.com/auth/devstorage.read_only');
	    $client->setAccessType('offline');
	    $httpClient = $client->authorize();
	    try{
	        $response = $httpClient->get($link);
	        $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if(isset($data->items)){
                foreach($data->items as $item){
                    if (str_contains($item->name, $request->bucket)) {
                        echo 'https://s3.wasabisys.com/'.$bucket.'/'.$item->name.'<br>';
                    }
                }
            }
	    }catch(Exception $e){
	        return $e;
	    }
	}
	
	public function gphotosLink($url){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $files = "";
	        $response = $client->get($url);
	        $get = $response->getBody()->getContents();
	        $data  = explode('url\u003d', $get);
            $data  = explode('url\u003d', $get);
            if (isset($data[1])) {
                $url    = explode('%3Dm', $data[1]);
                $decode = urldecode($url[0]);
            } else {
                $decode = "";
            }
            $count        = count($data);
            $linkDownload = array();
            $v1080p       = $decode . '=m37';
            $v720p        = $decode . '=m22';
            $v360p        = $decode . '=m18';
            if ($count > 7) {
                $linkDownload['360p']  = $v360p;
                $linkDownload['720p']  = $v720p;
                $linkDownload['1080p'] = $v1080p;
            } else if ($count > 3) {
                $linkDownload['360p'] = $v360p;
                $linkDownload['720p'] = $v720p;
            } else if ($count > 2) {
                $linkDownload['360p'] = $v360p;
            }
            foreach ($linkDownload as $key => $l) {
                $files .= '{"type": "video/mp4", "label": "' . $key . '", "file": "' . $l . '"},';
            }
            if (@!$files) {
                $files = '{"type": "video/mp4", "label": "HD", "file": "' . $decode . '=m18' . '"}';
            } else {
                return '[' . rtrim($files, ',') . ']';
            }
        }catch(Exception $e){
	        return 'Not found file';
        }
	}

    public function SolidLink($url){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
        try{
            $response = $client->get($url);
	        $data = $response->getBody()->getContents();
	        $video  = explode('"downloadUrl":"', $data);
            $data  = explode('"', $video[1]);
	        return $data[0];
        }catch(Exception $e){
            return $e->getMessage();
        }
	}
	
	public function aparatLink(){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
        try{
            $response = $client->get("https://wolfstream.tv/embed-q34todyjqo5w.html");
	        $data = $response->getBody()->getContents();
	        $video  = explode('sources: [{file:"', $data);
            $data  = explode('"}],', $video[1]);
	        dd($data[0]);
	        
        }catch(Exception $e){
            return $e->getMessage();
        }
	}

	public function degooLink($url){
		$parse = parse_url($url);
        $path = str_replace(array('/share/','/files/'),'',$parse['path']);
        if(isset($parse['fragment'])){
            $query = parse_str($parse['fragment'], $params);
            $id_episode = $params['ID'];
            $id_episode = explode('#',$id_episode);
            $id = $id_episode[0];
        }else{
            $id = null;
        }
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->post('https://rest-api.degoo.com/shared',
                ['body' => json_encode(
                    [
                        'HashValue' => $path,
                        'Limit' => 999,
                        'FileID' => null,
                        'JWT' => null
                    ]
                )]
            );
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if($data->Items){
                if($id){
                    foreach($data->Items as $item){
                        if($item->ID == $id){
                            return $item->URL;
                        }
                    }
                }else{
                    return $data->Items[0]->URL;
                }
            }else{
                $video = 'error';
            }
        }catch(\Exception $e){
	        return 'Error';
        }
	}
	
	public function VideosLink($id){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
            $response = $client->get("https://videos.sh/embed-".$id.".html");
            $data = $response->getBody()->getContents();
            $video = explode('sources: [{file:"',$data);
            $video = explode('"}]',$video[1]);
            return $video[0];
	    }catch(Exception $e){
	        return array(
                'msg' => $e->getMessage()
            );
	    }
	}
	
	public function ZplayerLink($id){
	    $client = new Client([
            'timeout'  => 5
        ]);
	    try{
            $response = $client->get("https://v2.zplayer.live/embed/".$id);
            $data = $response->getBody()->getContents();
            $www = explode("<script type='text/javascript'>", $data);
            $www = explode('</script>', $www[1]);
            $unpacker = new JavascriptUnpacker;
            $source = $unpacker->unpack($www[0]);
            $url = explode('[{file:"', $source);
            $url = explode('"}],', $url[1]);
            return $url[0];
	    }catch(Exception $e){
	        return array(
                'msg' => $e->getMessage()
            );
	    }
	}
	
	public function degooLinks($id){
	    $client = new Client([
            'timeout'  => 2.0
        ]);
	    try{
	        $response = $client->post('https://rest-api.degoo.com/shared',
                ['body' => json_encode(
                    [
                        'HashValue' => $id,
                        'Limit' => 999,
                        'FileID' => null,
                        'JWT' => null
                    ]
                )]
            );
            
            $data = $response->getBody()->getContents();
            $data = json_decode($data);
            if($data->Items){
                foreach($data->Items as $item){
                    echo 'https://cloud.degoo.com/share/'.$id.'#ID='.$item->ID.'#NAME='.$item->Name.'<br>';
                }
            }
        }catch(\Exception $e){
	        return array(
	            'msg' => $e->getMessage()
	        );
        }
	}
	
	public function gphotosLinks($bucket){
        $key = "AIzaSyBvrJNFJJGi8gX3Xpqnzjpb89TXaONvKZ0";
	    $userProject = "tuanimelatinohd";
	    $link = 'https://photoslibrary.googleapis.com/v1/mediaItems?key='.$key;
        $client = new \Google\Client();
        $client->setClientId("263892511913-kruokli4l2nsdrq16ibvgs6tjicm0ff8.apps.googleusercontent.com");
        $client->setClientSecret("NKB7d0sVN6MNbQsMiCEzFfxp");
        $client->addScope("email");
        $client->addScope("profile");
        
	    $httpClient = $client->authorize();
	    try{
	        $response = $httpClient->get($link,['timeout' => 1]);
            $data = $response->getBody()->getContents();
            dd($data);
	        return $this->url->__toString();
	    }catch(Exception $e){
	        return array(
                'msg' => $e->getMessage()
            );
	    }
	}
    
}