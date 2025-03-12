<?php

namespace App\Modules\Tasks\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use App\Modules\Tasks\Models\Task;
use App\Modules\Tasks\Resources\TaskResource;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class TaskService
{    
    private $PAGINATE = 20;
    private $PAGE = 1;            
    
    public function findAll($request)
    {            
        $query = Task::query();  

        $paginate = $this->PAGINATE;
        if ($request->has('paginate')){
            $paginate = $request->paginate;
        }                

        $page = $this->PAGE;
        if ($request->has('page')){
            $page = $request->page;            
        }    
        
        $user = auth()->user();
        
        $query->where('user_id', '=', $user->id);
        
        $filters = $request->input();       
        
        if (!empty($filters)){
            foreach ($filters as $key => $value){                                          
                $query->where($key, '=', $value);
            }
        }                              

        $query->select(
            'tasks.*'
        );        

        $query->orderBy('created_at', 'DESC');

        return $query->paginate($paginate, ['*'], 'page', $page);
    }   
    
    public function findById($id)
    {
        try{
            $user = auth()->user();
            
            $model = Task::where('id',$id)
                         ->where('user_id',$user->id)
                         ->get();   

            $code = 200;
            $message = 'Tarea obtenida exitosamente';
            $data = $model;

            if(is_null($model)){
                $code = 422;
                $message = 'Tarea no encontrada';
                $data = [];                
            }            
        } catch (\Exception $e) {
            $code    = 503;
            $message = $e->getMessage();
            $data = [];
        }

        $response = [
            "codigoRetorno" => $code,
            "glosaRetorno"  => $message,
            "timestamp"     => new \DateTime('NOW'),
            "respuesta"     => $data,
        ];       

        return $response;              
    }        
    
    public function create($request) 
    {        
        try {
            $user = auth()->user();            
            
            $model = Task::create(
            [
                'name' => $request->name,
                'user_id' => $user->id,
                'completed' => 0,
            ]);         

            $code = 200;
            $message = 'Tarea creada exitosamente';
            $data = $model;            
        } catch (\Exception $e) {
            $code    = 503;
            $message = $e->getMessage();
            $data = [];            
        }     

        $response = [
            "codigoRetorno" => $code,
            "glosaRetorno"  => $message,
            "timestamp"     => new \DateTime('NOW'),
            "respuesta"     => $data,
        ];       
        
        return $response;                      
    }
    
    public function delete($id)
    {
        try{
            $user = auth()->user();
            
            $model = Task::where('id','=', $id)
                         ->where('user_id',$user->id)
                         ->delete();    
            
            if ($model) {
                $code = 200;
                $message = 'Tarea eliminada correctamente';
                $data = $model;
            } else {
                $code = 422;
                $message = 'No se pudo eliminar la tarea. No existe la tarea ' . $id . ' o no tiene autorización para eliminar esa tarea';
                $data = [];
            }            
        } catch (Exception $ex) {
            $code    = 503;
            $message = $e->getMessage();
            $data = [];
        }    
        
        $response = [
            "codigoRetorno" => $code,
            "glosaRetorno"  => $message,
            "timestamp"     => new \DateTime('NOW'),
            "respuesta"     => $data,
        ];
        
        return $response;
    }        
    
    public function update($request)
    {      
        try {
            $user = auth()->user();
            
            $model = Task::where('id','=', $request->id)
                         ->where('user_id',$user->id) 
                         ->update($request->all());            
            
            $modelUpdate = Task::where('id','=', $request->id)
                         ->where('user_id',$user->id) 
                         ->get();
            
            $code = 200;
            $message = 'Datos actualizados correctamente';
            $data = $modelUpdate;                                       

            if(!$model){
                $code = 403;
                $message = 'Los datos no se pudieron actualizar. El Id ' . $request->id . ' no existe o no tiene autorización para modificar la información';
                $data = [];                                       
            }                        
        } catch (\Exception $e) {
            $code    = 503;
            $message = $e->getMessage();
            $data = [];            
        }        
        
        $response = [
            "codigoRetorno" => $code,
            "glosaRetorno"  => $message,
            "timestamp"     => new \DateTime('NOW'),
            "respuesta"     => $data,
        ];
        
        return $response;        
    }   
    
}
