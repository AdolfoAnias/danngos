<?php

namespace App\Modules\Tasks\Controllers;

use App\Modules\Tasks\Models\Task;
use Illuminate\Http\Request;
use App\Modules\Tasks\Resources\TaskResource;
use App\Modules\Tasks\Services\TaskService;
use App\Modules\Tasks\Services\Crypto;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Modules\Tasks\Requests\TaskRequest;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

#[Group('Tareas', 'Servicios de Tareas')]
class TaskController extends Controller
{
    private $service;
    private $crypto;
    
    public function __construct(TaskService $service, Crypto $cryptoService, )
    {
        $this->service = $service;
        $this->crypto = $cryptoService;            
    }    
        
    /**
    * Mostrar todas las tareas    
    * 
    * Obtiene todas las tareas del usuario autenticado
    * @authenticated      
    * @queryParam page integer Numero de página. Example: 2
    * @qyeryParam paginate integer Tamaño de página. Example: 20 
    */            
    public function index(Request $request)
    {        
        try {        
            $taskRequests = $this->service->findAll($request);

            $response = TaskResource::collection($taskRequests)->response()->getData(true);
            $response = ResponseHelper::returnResponse(200, "Servicio OK.", $response);
            //$response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $response = ResponseHelper::returnResponse(500, $e->getMessage());
           // $response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));
            return response()->json($response, 500);
        }        
    }

    /**
    * Guardar una tarea    
    * 
    * Guarda la información de una tarea creada por el usuario autenticado
    * @authenticated      
    * @bodyParam name string Nombre de la tarea. Example: Desarrollar metodo de login
    */        
    public function store(TaskRequest $request)
    {
        try {
            $service = $this->service->create($request);   
        
            $code    = $service['codigoRetorno'];
            $message = $service['glosaRetorno'];            
            $data    = $service['respuesta'];            
        } catch (Exception $ex) {
            $code    = 422;
            $message = $e->getMessage();
            $data    = [];            
        }        
        
        $response = ResponseHelper::returnResponse($code, $message, $data);
       // $response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));
        
        return response()->json($response, $code);        
    }

    /**
    * Mostrar una tarea    
    * 
    * Muestra la información de una tarea especifica para el usuario autenticado
    * @authenticated      
    * @urlParam id integer required El ID de la tarea. Example: 2
    */            
    public function show($id)
    {
        try {
            $service = $this->service->findById($id);

            $code    = $service['codigoRetorno'];
            $message = $service['glosaRetorno'];            
            $data    = $service['respuesta'];
        } catch (\Exception $e) {
            $code    = 422;
            $message = $e->getMessage();
            $data    = [];            
        }

        $response = ResponseHelper::returnResponse($code, $message, $data);
       // $response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));
        
        return response()->json($response, $code);
    }

    /**
    * Actualizar una tarea    
    * 
    * Actualiza la información de una tarea especifica para el usuario autenticado
    * @authenticated      
    * @queryParam completed boolean required Estado de la tarea. Example: true/false
    */            
    public function update(Request $request)
    {
        try {
            $service = $this->service->update($request);

            $code    = $service['codigoRetorno'];
            $message = $service['glosaRetorno'];            
            $data    = $service['respuesta'];
        } catch (\Exception $e) {
            $code    = 422;
            $message = $e->getMessage();
            $data    = [];            
        }

        $response = ResponseHelper::returnResponse($code, $message, $data);
        //$response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));
        
        return response()->json($response, $code);        
    }

    /**
    * Eliminar una tarea    
    * 
    * Elimina una tarea especifica para el usuario autenticado
    * @authenticated      
    * @urlParam id integer required El ID de la tarea. Example: 2
    */                
    public function destroy($id)
    {
        try{
            $service = $this->service->delete($id);

            $code    = $service['codigoRetorno'];
            $message = $service['glosaRetorno'];            
            $data = [];            
        } catch (\Exception $e) {
            $code    = 422;
            $message = $e->getMessage();
            $data    = [];            
        }
        
        $response = ResponseHelper::returnResponse($code, $message, $data);
        //$response = $this->crypto->cryptoJsAesEncrypt(json_encode($response));
        
        return response()->json($response, $code);        
    }
}
