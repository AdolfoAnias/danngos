<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Usuarios', 'Servicios de Usuarios')]
class LoginController extends Controller
{
    private $loginValidationRules = [
        'email' => 'required|email',
        'password' => 'required'
    ];    

    /**
    * Loguear un usuario    
    * 
    * Este servicio permite que un usuario acceda al sistema
    * 
    * @bodyParam email string required El correo del usuario. Example: pepe@gmail.com
    * @bodyParam password string required La contraseña del usuario.
    */    
    public function loginUser(Request $request) {
        $validateUser = Validator::make($request->all(), $this->loginValidationRules);

        if($validateUser->fails()){
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validateUser->errors()
            ], 401);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'El email y el password no corresponden con alguno de los usuarios',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'message' => 'Login correcto',
            'token' => $user->createToken("API ACCESS TOKEN")->plainTextToken
        ], 200);
    }
}
