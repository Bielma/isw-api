<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Devolucion;
use App\DetalleDevolucion;

class DevolucionController extends Controller
{
     public function index(){
        $devoluciones = Devolucion::all()->load('detalles');
        return response()->json([
           'code' => 200,
            'status' => 'succes',
            'devoluciones' => $devoluciones
        ]);
    }
    
    public function store(Request $request){    
        $json = $request->input('datos', null);        
        $params_array = json_decode($json, true);
        //var_dump($params_array); die();
        
        $devolucion = new Devolucion();  
        $devolucion -> folio_venta = $params_array['folioVenta'];
        $devolucion -> fecha = $params_array['fecha'];
        
        $devolucion->save();
        
                 
        $productos = $params_array['productos'];                                                    
       
        for($i= 0; $i<count($productos); $i++){
            $detalles = new DetalleDevolucion();            
            $detalles -> id_producto = $productos[$i]['codigo'];
            $detalles -> cantidad = $productos[$i]['cantidad'];  
            $detalles -> motivo = $productos[$i]['motivo'];          
            $detalles -> folio_devolucion = $devolucion['folio_devolucion'];
            $detalles -> save();            
        }                    
        //Respuesta de error. 
        $data = array(
          'status' => 'succes',
          'code' => '200',
            'message' => 'El registro se ha insertado con exito',
            'venta' => $devolucion
        );
        
        return  response() -> json($data, $data['code']);                
        
    }

    public function show($folio){
        $devolucion = Devolucion::find($folio) ->load('detalles');
        if(is_object($devolucion)){
            $data = [
                'code' => 200,
                'status' => 'succes',
                'product' => $devolucion
            ];            
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Devolución no existe'
            ];
        }       
        return response()-> json($data, $data['code']);

    }
}
