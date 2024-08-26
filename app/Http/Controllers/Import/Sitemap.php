<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Storage;

class Sitemap extends Controller
{
    public function generate(){
        try{
            $file_content = file_get_contents('https://api.animelatinohd.com/site.xml');
            if(Storage::disk('public_uploads')->put('sitemap.xml', $file_content)) {
                return 'Sitemap generate successfully';
            }
        }catch(Exception $e){
            Log::info($e->getMessage());
            return $e->getMessage();
        } 
    }
}
