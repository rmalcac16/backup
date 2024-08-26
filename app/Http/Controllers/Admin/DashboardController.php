<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Anime;
use App\Models\Episode;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $users = User::orderby('id','desc')->limit(10)->get();
        $animes = Anime::orderby('id','desc')->limit(10)->get();
        $episodes = Episode::orderby('views_app','desc')->limit(10)->get();
        $data = [
            'category_name' => 'dashboard',
            'page_name' => 'analytics',
            'users' => $users,
            'animes' => $animes,
            'episodes' => $episodes
        ];
        return view('admin.home.dashboard')->with($data);
    }
    
}