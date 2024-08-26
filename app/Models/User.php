<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Animelhd\AnimesFavorite\Traits\Favoriter;
use Animelhd\AnimesView\Traits\Viewer;
use Animelhd\AnimesWatching\Traits\Watchinger;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use Favoriter;
	use Viewer;
	use Watchinger;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getToken($request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        $user = $this->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return array(
                'msg' => 'Usuario y/o contraseña incorrectos.'
            );
        }
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function getTokenApp($request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        $user = $this->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return array(
				'code' => 400,
                'msg' => 'Usuario y/o contraseña incorrectos.'
            );
        } else {
            if($user->isPremium){
                if ($user->tokens()->exists()) {
                    $user->tokens()->delete();
                    return array(
                        'code' => 200,
                        'token' => $user->createToken($request->device_name)->plainTextToken,
                        'user' => $user
                    );
                }else{
                    return array(
                        'code' => 200,
                        'token' => $user->createToken($request->device_name)->plainTextToken,
                        'user' => $user
                    );
                }
            }else{
                return array(
                    'code' => 200,
                    'token' => $user->createToken($request->device_name)->plainTextToken,
                    'user' => $user
                );
            }
		}
    }

    public function register($request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|confirmed|min:7',
        ]);
        
        $email = $this->where('email', $request->email)->first();
        $name = $this->where('name', $request->name)->first();

        if($email && $name) {
            return array(
				'code' => 400,
                'msg' => 'Este Email y Username ya estan en uso'
            );
        } else if($email) {
            return array(
				'code' => 400,
                'msg' => 'Este Email ya esta en uso'
            );            
        } else if($name) {
            return array(
				'code' => 400,
                'msg' => 'Este Username ya esta en uso'
            );
        } else {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return array(
                'code' => 200,
                'msg' => 'Registro Exitoso'
            );
        }
    } 

    public function login($request)
    {
        return $request->user();
    }

    public function logout($request)
    {
        return $request->user()->currentAccessToken()->delete();
    }
    
    public function forgotPassword($request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:7',
            'password_confirmation' => 'required'
        ]);

        $user = User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);
        
        return array(
            'code' => 200,
            'msg' => 'Registro Exitoso'
        );
     }

     public function vipUsers($request)
     {
         $request->validate([
             'email' => 'required|email|exists:users'
         ]);
 
         $user = User::where('email', $request->email)
         ->update(['isPremium' => $request->is_premium]);
         
         return array(
             'code' => 200,
             'msg' => 'Registro Exitoso'
         );
      }

}
