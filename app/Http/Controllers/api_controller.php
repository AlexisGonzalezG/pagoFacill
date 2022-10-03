<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class api_controller extends Controller
{
    public function operation(Request $request)
    {
        $business_errors = [];
        $response = [];
        $authorized = true;
        $company_name = null;
        $product_id = null;
        $date_operation = date("Y-m-d H:i:s");

        if($request['amount'] == "" || $request['amount'] == NULL)
        $amount = 0;
        else
        $amount = $request['amount'];

        $companys = DB::table('companys')
            ->where([
                ['id', $request['company_id']], 
                ['status', '=', 1]])
            ->get(); // VALIDA SI COMPAÑIA EXISTE.

        if (count($companys) < 1) {
            $authorized = false;
            $business_errors[] = ["EL ID DE LA COMPAÑIA NO EXISTE EN BASE DE DATOS."];
        } else {
            $company_name = $companys[0]->name;

            $products = DB::table('products')
                ->where([
                    ['name', $request['product_name']], 
                    ['status', '=', 1]])
                ->get(); // VALIDA SI EXISTE PRODUCTO.

            if (count($products) < 1) {
                $authorized = false;
                $business_errors[] = ["PRODUCTO NO ENCONTRADO."];
            } else {
                $product_id = $products[0]->id;

                $company_product = DB::table('company_product')
                    ->where([
                        ['company_id', $request['company_id']], 
                        ['product_id', $product_id], 
                        ['status', '=', 1]])
                    ->get(); // VALIDA SI LA COMPAÑIA PUEDE OPERAR CON EL PRODUCTO SOLICITADO (PRODUCT).

                if (count($company_product) < 1) {
                    $authorized = false;
                    $business_errors[] = ["ÉSTA COMPAÑIA NO OPERA CON EL PRODUCTO SOLICITADO."];
                }
            }

            $exists = DB::table('companys')
                ->where([
                    ['amount_min', '<=', $amount], 
                    ['amount_max', '>=', $amount], 
                    ['id', '=', $request['company_id']]])
                ->exists(); // VALIDA SI EL MONTO SE ENCUENTRA ENTRE EL RANGO DEL MONTO MINIMO Y MAXIMO DE LA COMPAÑIA.

            if (!$exists) {
                $authorized = false;
                $business_errors[] = ["EL MONTO NO SE ENCUENTRA DENTRO DEL RANGO PERMITIDO POR LA COMPAÑIA."];
            }
        }

        $response = [
            "authorized" => $authorized,
            "company_name" => $company_name,
            "product" => [
                "id" => $product_id,
                "type" => $request['product_name'],
                "amount" => $request['amount'],
                "date_operation" => $date_operation,
            ],
            "business_errors" => $business_errors,
        ]; // GENERA JSON RESPONSE

        $insert = DB::table('operations')->insert([
            'authorized' => $authorized,
            'company_id' => $request['company_id'],
            'product_id' => $product_id,
            'company_name' => $company_name,
            'product_name' => $request['product_name'],
            'amount' => $request['amount'],
        ]); //INSERTA DATOS DE OPERACION

        if($authorized) return ['ok' => 100,'data' => json_encode($response)];
        
        else return ['ok' => 0,'data' => json_encode($response)];
        
    }
    public function get_operations(Request $request)
    {

        $operations = DB::table('operations')
                ->where([
                    ['authorized', '=', true], 
                    ['status', '=', 1]])
                ->get(); // OBTENER OPERACIONES AUTORIZADAS.


                if(count($operations)>0){
                    return ['ok' => 100,
                    'data' => json_encode($operations)];
                }
                else{
                    return ['ok' => 0];
                }
        
    }
}
