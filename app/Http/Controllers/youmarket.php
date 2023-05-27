<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use stdClass;

class youmarket extends Controller
{
    public function getYouMarket(Request $request)
    {   
        $result = new stdClass();
        $result->data = (object)["message"=>"","count"=> 0,"porcentaje"=> 0];
        $mutacion = false;

        $diagonales = [];
        $diagonalMutante = [];

        try {
            // Validamos el unico campo que necesitamos.
            $request->validate([
                "cadena"=>"required|string|min:4"
            ]);
    
            // Extraemos la cadena
            $cadena = $request->get("cadena");

            // Contamos cantidad de daracteres y sacamos la raiz cuadrada del mismo redondendo el resultado 
            $lengthCadena = strlen($cadena);
            $result->data->count = $lengthCadena;

            $chunkDiv = ceil(sqrt($lengthCadena));

            // Verificamos tenemos una matris cuadrada
            if (!($chunkDiv*$chunkDiv === (float)$lengthCadena)){
                throw new Exception("La cantidad de caracteres que utilizadas no conforman una matris par.");
            
            }
            
            $chunkDiv = (int)$chunkDiv;
            
            // Separamos en chunks
            $chunk = array_chunk(str_split($cadena), $chunkDiv);

            // Obtener las diagonales principales
            for ($i = 0; $i < $chunkDiv; $i++) {
                $diagonales["primera"][] = $chunk[$i][$i];
                $diagonales["segunda"][] = $chunk[$i][$chunkDiv - $i - 1];
            }
            
            // Contamos la cantidad de datos iguales que se repiten
            foreach ($diagonales as $key => $value) {
                $diagonalJob = array_count_values($value);

                foreach ($diagonalJob as $count) {
                    if (intval($count) % 2 === 0) {
                        $diagonalMutante[$key] = $value;
                        $mutacion = true;
                        break;
                    }
                }
            }

            if ($mutacion){
                $result->data->message = "La cadena de caracteres que emitio contiene una mutacion.";
                $result->data->porcentaje = (count($diagonalMutante)/ 2) * 100;
            }else{
                $result->data->message = "Libre de mutaciones";
            }
            
            return $result;
        } catch (Exception $e) {
            $result->data->message = $e->getMessage();
            return json_encode($result);
        }
    }
}
