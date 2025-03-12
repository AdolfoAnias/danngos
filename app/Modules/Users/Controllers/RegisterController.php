<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Usuarios', 'Servicios de Usuarios')]
class RegisterController extends Controller
{
    private $registerValidationRules = [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required'
    ];    
    
    /**
    * Registrar un usuario    
    * 
    * Este servicio permite registrar un usuario en el sistema
    * 
    * @bodyParam name string required El nombre del usuario. Example: pepe
    * @bodyParam email string required El correo del usuario. Example: pepe@gmail.com
    * @bodyParam password string required La contraseña del usuario. Example: secret
    */    
    public function registerUser(Request $request) {
        $validateUser = Validator::make($request->all(), $this->registerValidationRules);
        
        if($validateUser->fails()){
            return response()->json([
                'message' => 'Ha ocurrido un error de validación',
                'errors' => $validateUser->errors()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'El usuario se ha creado',
            'token' => $user->createToken("API ACCESS TOKEN")->plainTextToken
        ], 200);
    }        
}
