<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * model instances.
     */
    protected $user;	

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\User;  $user
     * @return void
     */	
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	public $url;

    public function updateProfile(Request $request)
	{
		try {
			$name = $request->get('name');
			$image = $request->get('image');
			$namev = $this->user->where('name', $name)->first();
			$userUpdate = $this->user::find($request->id);
			if($userUpdate->name != $name) {
				if($namev) {
					return array(
						'code' => 400,
						'msg' => 'Este Username ya estan en uso'
					);
				} else {
					$userUpdate->name = $name;
					if($this->validateImage($image) && $image != null) {
						$userUpdate->image = $image;
					}else {
						$userUpdate->image = "https://avatarfiles.alphacoders.com/319/319952.jpg";
					}
					$userUpdate->save();
					return array(
						'status' => 'OK',
						'code' => 200,
						'data' => $userUpdate
					);					
				}
			} else {
				$userUpdate->name = $name;
				if($this->validateImage($image) && $image != null) {
					$userUpdate->image = $image;
				}else {
					$userUpdate->image = "https://avatarfiles.alphacoders.com/319/319952.jpg";
				}
				$userUpdate->save();
				return array(
					'status' => 'OK',
					'code' => 200,
					'data' => $userUpdate
				);
			}
		} catch (Exception $error) {
			return array(
				'status' => 'Error',
				'msg' => $error->getMessage(),
				'code' => $error->getCode(),
				'detailed' => $error->getLine()
			);
		}
	}


	public function validateImage($url){
		$extension = pathinfo($url, PATHINFO_EXTENSION);
		$extensionesImagen = array("jpg", "jpeg", "png", "gif", "bmp");
		if (in_array(strtolower($extension), $extensionesImagen)) {
			return true;
		} else {
			return false;
		}
	}
}